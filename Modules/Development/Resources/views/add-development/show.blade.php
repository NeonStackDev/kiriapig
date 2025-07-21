@extends('layouts.app')

@section('title', __('development::lang.show_development'))

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<!-- Content Header -->
<section class="content-header">
    <h1>@lang('development::lang.show_development')</h1>
    <small>@lang('development::lang.view_development_details')</small>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="row">
                    {{-- Document Information --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('development::lang.doc_no')</label>
                            <p>{{ $development->doc_no }}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.datetime')</label>
                            <p>{{ $development->datetime }}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.module')</label>
                            <p>{{ $development->module->name }}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.type')</label>
                            <p>{{ $development->type }}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.priority')</label>
                            <p>{{ $development->priority }}</p> 
                        </div>

                        <div class="form-group">
                             <label>@lang('development::lang.status')</label>
                             <span class="label label-{{ $development->status === 'Completed' ? 'success' : ($development->status === 'Not Completed' ? 'warning' : 'primary') }}">
                                 @lang('development::lang.status_' . Str::snake($development->status))
                             </span>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('development::lang.details')</label>
                            <p>{!! $development->details !!}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.related_doc_no')</label>
                            <p>{{ $development->related_doc_no }}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.visible_to_groups')</label>
                            <p>{{ implode(', ', \App\UserGroup::whereIn('id', $development->visible_to_groups)->pluck('name')->toArray()) }}</p>
                        </div>

                        <div class="form-group">
                            <label>@lang('development::lang.created_by')</label>
                            <p>{{ optional($development->user)->username }}</p>
                        </div>
                    </div>

                    {{-- Status Notes --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('development::lang.status_notes')</label>
                            @php
                                $filteredNotes = [];
                                if (!empty($development->status_notes) && is_array($development->status_notes)) {
                                    foreach ($development->status_notes as $note) {
                                        if (is_array($note) && !empty($note['note'])) {
                                            $noteText = trim($note['note']);
                                            if ($noteText !== '') {
                                                $filteredNotes[] = [
                                                    'note' => $noteText,
                                                    'created_at' => $note['created_at'] ?? now(),
                                                    'user_id' => $note['user_id'] ?? null
                                                ];
                                            }
                                        } elseif (is_string($note) && trim($note) !== '') {
                                            $filteredNotes[] = [
                                                'note' => trim($note),
                                                'created_at' => now(),
                                                'user_id' => auth()->id()
                                            ];
                                        }
                                    }
                                }
                            @endphp
                            
                            @if(!empty($filteredNotes))
                                @foreach($filteredNotes as $note)
                                    <div class="alert alert-info mb-2">
                                        <div class="d-flex justify-content-between">
                                            <div>{{ $note['note'] }}</div>
                                            <small class="text-muted">
                                                {{ !empty($note['created_at']) ? \Carbon\Carbon::parse($note['created_at'])->format('Y-m-d H:i') : '' }}
                                                @if(!empty($note['user_id']))
                                                    <br>
                                                    @php
                                                        $user = \App\User::find($note['user_id']);
                                                    @endphp
                                                    @if($user)
                                                        <i>@lang('development::lang.created_by'): {{ $user->username }}</i>
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">@lang('development::lang.no_status_notes')</p>
                            @endif
                        </div>
                    </div>

                    {{-- Group Comments --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('development::lang.group_comments')</label>
                            <div class="card-group">
                                @foreach($user_groups as $groupId => $groupName)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <strong>{{ $groupName }}</strong>
                                        </div>
                                        <div class="card-body">
                                             @if(!isset($group_comments[$groupId]) || empty($group_comments[$groupId]))
                                                 <p class="text-muted">@lang('development::lang.no_comment')</p>
                                             @else
                                                 @php
                                                     $comments = collect($group_comments)->filter(function($comment) use ($groupId) {
                                                         return isset($comment['user_id']) && $comment['user_id'] != null;
                                                     });
                                                 @endphp
                                                 
                                                 @foreach($comments as $comment)
                                                     <div class="row mb-3">
                                                         <div class="col-md-6">
                                                             <p><strong>@lang('development::lang.comment_type'):</strong> 
                                                                 {{ $comment['comment_type'] }}</p>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <p><strong>@lang('development::lang.user'):</strong> 
                                                                 {{ optional(\App\User::find($comment['user_id']))->username }}</p>
                                                         </div>
                                                     </div>
                                                 @endforeach
                                             @endif
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-right">
            <a href="{{ route('list-development.index') }}" class="btn btn-default">@lang('development::lang.general.back')</a>
            <a href="{{ route('development.edit', $development->id) }}" class="btn btn-primary">@lang('development::lang.general.edit')</a>
        </div>
    </div>
</section>
</div>
@endsection
