<?php

namespace Modules\MPCS\Http\Controllers;

use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Store;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Modules\MPCS\Entities\Mpcs15FormDetails;
use Modules\MPCS\Entities\FormF15TransactionData; 
use Modules\MPCS\Entities\FormF15Header; 
use App\Contact;
use App\Transaction;
class F15FormController extends Controller
{ 
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;
 
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }

    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $business_id = request()->session()->get('business.id');

        // Get F15 settings from DB
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();

        // F15 starting number
        $starting_form_number = $settings->F15_form_sn ?? 1;

        // Count how many forms already created that have valid 'today' data (adjust the condition as needed)
        $formCount = FormF15Header::where('business_id', $business_id)
            ->whereHas('details', function ($query) {
                $query->where('rupees', '>', 0); // replace with your actual column
            })
            ->count();


        $next_form_number = $starting_form_number + $formCount;

        // Other Data
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_name = BusinessLocation::where('business_id', $business_id)->value('name');
        $currency_precision = Business::where('id', $business_id)->value('currency_precision');

        // Get Header if user has created one already
        $row = FormF15Header::where('created_by', auth()->user()->id)->first();
        $headers = (!empty($row) && (auth()->user()->can('user') || auth()->user()->id === $row->created_by)) ? $row : null;

        return view('mpcs::forms.F15')->with(compact(
            'business_locations',
            'settings',
            'suppliers',
            'next_form_number',
            'headers',
            'business_name',
            'currency_precision'
        ));
    }


    public function getFormF15Data(Request $request)
    {
        $startDate = $request->input('startDate') ?? date('Y-m-d');
        $endDate = $request->input('endDate') ?? date('Y-m-d');

        $businessId = auth()->user()->business_id;
        $previousDate = date('Y-m-d', strtotime($startDate . ' -1 day'));

        // Form 22 check
        $isF22SavedToday = DB::table('form_f22_headers')
            ->where('business_id', $businessId)
            ->whereDate('form_date', $startDate)
            ->exists();

        // Last F22 (before today)
        $lastF22 = DB::table('form_f22_headers')
            ->where('business_id', $businessId)
            ->whereDate('form_date', '<', $startDate)
            ->orderBy('form_date', 'desc')
            ->first();

        $lastF22Total = 0;
        if ($lastF22) {
            $lastF22Total = DB::table('form_f22_details')
                ->where('header_id', $lastF22->id)
                ->sum('sales_price_total');
        }

        // Total for F22 on current date
        $currentF22Total = DB::table('form_f22_headers')
            ->join('form_f22_details', 'form_f22_headers.id', '=', 'form_f22_details.header_id')
            ->where('form_f22_headers.business_id', $businessId)
            ->whereDate('form_f22_headers.form_date', $startDate)
            ->sum('form_f22_details.sales_price_total');

        // F15 header current & previous
        $currentF15Header = DB::table('mpcs_form_f15_headers')
            ->where('business_id', $businessId)
            ->whereDate('dated_at', $startDate)
            ->first();

        $previousF15Header = DB::table('mpcs_form_f15_headers')
            ->where('business_id', $businessId)
            ->whereDate('dated_at', $previousDate)
            ->first();

        $previousBalanceRow = null;
        if ($previousF15Header) {
            $previousBalanceRow = DB::table('mpcs_form_f15_details')
                ->join('form_f15_transaction_data', 'form15_label_id', '=', 'form_f15_transaction_data.id')
                ->where('f15_form_id', $previousF15Header->id)
                ->where('description', 'Balance Stock in Sale Price')
                ->first();
        }

        // Load F15 rows
        $rows = DB::table('form_f15_transaction_data')->get();

        $totalRow = [
            'previous' => 0,
            'today' => 0,
            'as_of' => 0
        ];

        $final = [];

        foreach ($rows as $row) {
            $row->previous_date_rupees = 0;
            $row->today_rupees = 0;
            $row->as_of_today_rupees = 0;

            // Opening Stock logic
            if ($row->description === 'Opening Stock') {
                // k. Up to Previous
                $row->previous_date_rupees = $isF22SavedToday ? 0 : $lastF22Total;

                // l. Today
                $row->today_rupees = $isF22SavedToday
                    ? $currentF22Total
                    : ($previousBalanceRow->rupees ?? 0);

                $row->as_of_today_rupees = $row->previous_date_rupees + $row->today_rupees;
            }

            // Grand Total = Total + Opening Stock
            if ($row->description === 'Grand Total') {
                $openingStock = collect($final)->firstWhere('description', 'Opening Stock');

                $row->previous_date_rupees = $totalRow['previous'] + ($openingStock->previous_date_rupees ?? 0);
                $row->today_rupees = $totalRow['today'] + ($openingStock->today_rupees ?? 0);
                $row->as_of_today_rupees = $totalRow['as_of'] + ($openingStock->as_of_today_rupees ?? 0);
            }

            // Track row values for Total row (m)
            if (!in_array($row->description, ['Total', 'Opening Stock', 'Grand Total'])) {
                $totalRow['previous'] += floatval($row->previous_date_rupees);
                $totalRow['today'] += floatval($row->today_rupees);
                $totalRow['as_of'] += floatval($row->as_of_today_rupees);
            }

            $final[] = $row;
        }

        // Now override Total row values
        foreach ($final as &$row) {
            if ($row->description === 'Total') {
                $row->previous_date_rupees = $totalRow['previous'];
                $row->today_rupees = $totalRow['today'];
                $row->as_of_today_rupees = $totalRow['as_of'];
            }
        }

        return response()->json(['data' => $final]);
    }



// TAB 2
    //By Zamaluddin : Time 09:00 AM : 29 January 2025
     public function get15FormSetting() {

        return view('mpcs::forms.partials.create_15_form_settings');

    }

 

 public function store15FormSetting(Request $request)
    {
        DB::beginTransaction();
        try {
            $business_id = session()->get('user.business_id');
            $id_form_labels = $request->input('form15_label_id', []);
            $rupees = $request->input('rupees', []);
            
            $header = FormF15Header::create([
                'business_id' => $business_id,
                'dated_at' => $request->input('dated_at', date('Y-m-d')),
                'created_by' => auth()->user()->id,
            ]);
            
            $data_to_insert_settings = [];
            foreach ($id_form_labels as $key => $id_form_label) {
                $data_to_insert_settings[] = [
                    'f15_form_id' => $header->id,
                    'form15_label_id' => $id_form_label,
                    'rupees' => $rupees[$key] ?? 0,
                ];
            }
            Mpcs15FormDetails::insert($data_to_insert_settings);

            DB::commit();
return redirect()->back()->with('success', __('mpcs::lang.form_15_settings_add_success'));
            
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => 0, 'msg' => __('mpcs::lang.form_15_settings_add_failed'), 'error' => $e->getMessage()], 500);
        }
    }








    public function mpcs15FormSettings()
{
    if (request()->ajax()) {
        $business_id = session()->get('business.id');

        $header = Mpcs15FormDetails::with(['fheader'])
            ->whereHas('fheader', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })
            ->orderBy('id', 'ASC')
            ->get();
            
    
        return DataTables::of($header)
            ->addColumn('action', function ($row) {
                if (auth()->user()->can('superadmin')) {
                return '
                    <button type="button" 
                        data-href="' . url('/mpcs/edit-15-form-settings/' . $row->id) . '" 
                        class="btn-modal btn btn-primary btn-xs" 
                        data-container=".update_form_15_settings_modal">
                        <i class="fa fa-edit"></i> Edit
                    </button>';
                    
                    
                }else{
                    return '';
                }
                    // <button type="button" 
                    //     data-href="' . url('/mpcs/delete-15-form-settings/' . $row->id) . '"
                    //     class="btn btn-danger btn-xs" 
                    //     onclick="deleteFormSetting(this)">
                    //     <i class="fa fa-trash"></i> Delete
                    // </button>
            })
            ->editColumn('dated_at', function ($row) {
                return !empty($row->fheader->dated_at) ? date('Y-m-d', strtotime($row->fheader->dated_at)) : '-';
                
                
                
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $business_id = session()->get('business.id');
    $business_locations = BusinessLocation::forDropdown($business_id);
    return view('mpcs::forms.form_15', compact('business_locations'));
}



public function edit15FormSetting($id)
{
    if (request()->ajax()) {
        $business_id = session()->get('business.id');
        
        $settings = Mpcs15FormDetails::where('id', $id)
                                   ->first();

        return view('mpcs::forms.partials.edit_15_form_settings')
               ->with(compact('settings'));
    }
}
    
    public function mpcs15Update($id, Request $request)
    {
        DB::beginTransaction();
    
        try {
            $business_id = session()->get('user.business_id'); 
            $rupees = $request->input('rupees'); 
     
     
            Mpcs15FormDetails::where('id',$id)->update(['rupees' => $rupees]);
    
            DB::commit(); 
    
            return response()->json([
                'success' => 1,
                'msg' => __('mpcs::lang.form_15_settings_update_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function delete15FormSetting($id){
    try {
        $business_id = session()->get('business.id');
        $formSettings = Mpcs15FormDetails::where('id', $id)
                                          ->delete();

        if ($formSettings) {
            $output = [
                'success' => true,
                'msg' => __('Delete Success')
            ];
        } else {
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    } catch (\Exception $e) {
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }

    return response()->json($output);
}

}
