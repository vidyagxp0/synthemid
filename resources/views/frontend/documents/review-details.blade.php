@extends('frontend.layout.main')
@section('container')
    {{-- ======================================
                    DOCUMENT TRACKER
    ======================================= --}}
    <div id="document-tracker">
        <div class="container-fluid">
            <div class="tracker-container">
                <div class="row">

                    <div class="col-12">
                        <div class="inner-block doc-info-block">
                            <div class="top-block">
                                <div class="title">
                                    {{ $document->document_name }}
                                </div>
                                <div class="buttons">
                                    <button onclick="location.href='{{ url('audit-trial', $document->id) }}';" style="cursor:pointer;">
                                        Audit Trail
                                    </button>
                                    @php $showEdit = false; @endphp
                                    @if (Helpers::checkRoles(2) AND Helpers::checkRoles_check_reviewers($document))
                                        @if (empty($review_reject))
                                            @if (empty($stagereview_submit))
                                                @php
                                                    $showEdit = true;
                                                @endphp
                                            @endif
                                        @elseif($document->stage == 4)
                                            @php
                                                $showEdit = true;
                                            @endphp
                                        @endif
                                    @endif
                                    
                                    @if (Helpers::checkRoles(2))
                                        @if (empty($hod_reject))
                                            @if (empty($stagehod_submit))
                                                @php
                                                    $showEdit = true;
                                                @endphp
                                            @endif
                                        @elseif($document->stage == 2)
                                            @php
                                                $showEdit = true;
                                            @endphp
                                        @endif
                                    @endif

                                    @if (Helpers::checkRoles(1) AND Helpers::checkRoles_check_approvers($document))
                                        @if (empty($approval_reject))
                                            @if (empty($stageapprove_submit))
                                                @php
                                                    $showEdit = true;
                                                @endphp
                                            @endif
                                        @elseif($document->stage == 6)
                                            @php
                                                $showEdit = true;
                                            @endphp    
                                        @endif

                                    @endif

                                    @if ($showEdit)
                                    {{-- <a href="{{ route('documents.edit', $document->id) }}" class="button">Edit</a> --}}
                                    <button onclick="location.href='{{ route('documents.edit', $document->id) }}';" style="cursor:pointer;">Edit</button>

                                        {{-- <button ><a href="{{ route('documents.edit', $document->id) }}">Edit</a></button> --}}
                                    @endif

                                    <button onclick="location.href='{{ url('documents/generatePdf', $document->id) }}';">Download
                                    </button>
                                    <button onclick="location.href='{{ url('documents/printPDF', $document->id) }}';"
                                            target="__blank">Print
                                    </button>
                                </div>
                            </div>
                            <div class="bottom-block">
                                <div>
                                    <div class="head">Document Number</div>
                                    <div>000{{ $document->id }}</div>
                                </div>
                                {{-- <div>
                                    <div class="head">Department</div>
                                    <div>{{ $document->department_name->name }}</div>
                                </div> --}}
                                <div>
                                    <div class="head">Document Type</div>
                                    <div>{{ $document->document_type_id }}</div>
                                </div>
                                <div>
                                    <div class="head">Working Status</div>
                                    <div>{{ Helpers::getDocStatusByStage($document->stage) }}</div>
                                </div>
                                <div>
                                    <div class="head">Last Modified By</div>
                                    @if ($document->last_modify)
                                        <div>{{ $document->last_modify->user_name }}</div>
                                    @else
                                        <div>{{ $document->oreginator->name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <div class="head">Last Modified On</div>
                                    @if ($document->last_modify)
                                        <div>{{ $document->last_modify->created_at }}</div>
                                    @else
                                        <div>{{ $document->created_at }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (in_array(Auth::user()->id, explode(",", $document->drafters)) && $document->stage == 2)
                        <div class="col-8">
                            <div class="inner-block tracker">
                                <div class="d-flex justify-content-between align-items-center hods">
                                    <div class="main-title">
                                        Record Workflow
                                    </div>
                                    <div class="buttons"> 
                                        @if (empty($draft_reject))
                                            @if ($drafter && empty($drafter_submit))
                                                @if($document->stage < 3)
                                                <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                @endif
                                            @endif
                                        @elseif($document->stage == 2)
                                            <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                            </button>
                                        @endif

                                        @if (empty($drafter) && $document->stage == 2)
                                            @if (empty($draft_reject))
                                                <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    Draft Complete&nbsp;<i class="fa-regular fa-paper-plane"></i>
                                                </button>
                                            @elseif($document->stage == 2)
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    Draft Complete&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="status">
                                    <div class="head">Current Status</div>
                                    <div class="progress-bars">
                                        
                                        @if ($document->stage < 13)
                                            {{-- @if ($document->stage >= 1)
                                                <div class="active">Initiate</div>
                                            @else
                                                <div>Initiate</div>
                                            @endif --}}
                                            @if ($document->stage >= 2)
                                                <div class="active">Pending Draft Creation</div>
                                            @else
                                                <div>Pending Draft Creation</div>
                                            @endif
                                            @if ($drafter)
                                                @if ($drafter->stage == 'HOD Review Submit' AND $document->stage >= 2)
                                                    <div class="active">Send for HOD Review</div>
                                                @else
                                                    <div>Send for HOD Review</div>
                                                @endif
                                            @else
                                                <div>Send for HOD Review</div>
                                            @endif
                                        @else 
                                            <div class="bg-danger rounded-pill text-white">{{ Helpers::getDocStatusByStage($document->stage) }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array(Auth::user()->id, explode(",", $document->hods)) && $document->stage == 3)
                        <div class="col-8">
                            <div class="inner-block tracker">
                                <div class="d-flex justify-content-between align-items-center hods">
                                    <div class="main-title">
                                        Record Workflow
                                    </div>
                                    <div class="buttons"> 
                                        @if (empty($hod_reject))
                                            @if ($stagehod && empty($stagehod_submit))
                                                @if($document->stage < 4)
                                                <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    More Info Required&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                @endif
                                            @endif
                                        @endif

                                        @if (empty($stagehod) && $document->stage == 3)
                                            @if (empty($hod_reject))
                                                <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                   More Info Required&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    HOD Review Complete&nbsp;<i class="fa-regular fa-paper-plane"></i>
                                                </button>
                                            @elseif($document->stage == 3)
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    HOD Review Complete&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="status">
                                    <div class="head">Current Status</div>
                                    <div class="progress-bars">
                                        
                                        @if ($document->stage < 13)
                                            {{-- @if ($document->stage > 1)
                                                <div class="active">Initiate</div>
                                            @else
                                                <div>Initiate</div>
                                            @endif --}}
                                            @if ($document->stage > 2)
                                                <div class="active">HOD Review</div>
                                            @else
                                                <div>HOD Review</div>
                                            @endif
                                            @if ($stagehod)
                                                @if ($stagehod->stage == 'HOD Review Submit' AND $document->stage >= 3)
                                                    <div class="active">QA Initial Review</div>
                                                @else
                                                    <div>QA Initial Review</div>
                                                @endif
                                            @else
                                                <div>QA Initial Review</div>
                                            @endif
                                        @else 
                                            <div class="bg-danger rounded-pill text-white">{{ Helpers::getDocStatusByStage($document->stage) }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array(Auth::user()->id, explode(",", $document->qa)) && $document->stage == 4)
                        <div class="col-8">
                            <div class="inner-block tracker">
                                <div class="d-flex justify-content-between align-items-center hods">
                                    <div class="main-title">
                                        Record Workflow
                                    </div>
                                    <div class="buttons"> 
                                        @if (empty($qa_reject))
                                            @if ($stageqa && empty($qa_submit))
                                                @if($document->stage < 5)
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    Send to HOD/ Author&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                @endif
                                            @endif
                                        @endif

                                        @if (empty($stageqa) && $document->stage == 4)
                                            @if (empty($qa_reject))
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    Send to Author&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    More Info Required &nbsp;<i class="fa-regular fa-paper-plane"></i>
                                                </button>
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                   QA Initial Review Complete&nbsp;<i class="fa-regular fa-paper-plane"></i>
                                                </button>
                                            @elseif($document->stage == 4)
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    QA Initial Review Complete&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="status">
                                    <div class="head">Current Status</div>
                                    <div class="progress-bars">
                                        
                                        @if ($document->stage < 13)
                                            {{-- @if ($document->stage > 1)
                                                <div class="active">Initiate</div>
                                            @else
                                                <div>Initiate</div>
                                            @endif --}}
                                            @if ($document->stage > 3)
                                                <div class="active">QA Initial Review</div>
                                            @else
                                                <div>QA Initial Review</div>
                                            @endif
                                            @if ($stageqa)
                                                @if ($stageqa->stage == 'QA Review Submit' AND $document->stage >= 4)
                                                    <div class="active">Reviewer's Review </div>
                                                @else
                                                    <div>Reviewer's Review</div>
                                                @endif
                                            @else
                                                <div>Reviewer's Review</div>
                                            @endif
                                        @else 
                                            <div class="bg-danger rounded-pill text-white">{{ Helpers::getDocStatusByStage($document->stage) }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array(Auth::user()->id, explode(",", $document->reviewers)) && $document->stage == 5)
                        <div class="col-8">
                            <div class="inner-block tracker">
                                <div class="d-flex justify-content-between align-items-center hods">
                                    <div class="main-title">
                                        Record Workflow
                                    </div>
                                    <div class="buttons"> 
                                        @if (empty($review_reject))
                                            @if ($stagereview && empty($stagereview_submit))
                                                @if($document->stage < 6)
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    More Info Required&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                @endif
                                            @endif
                                        @endif

                                        @if (empty($stagereview) && $document->stage == 5)
                                            @if (empty($review_reject))
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    Send to Initiator&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    More Info Required&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                   Review Complete&nbsp;<i class="fa-regular fa-paper-plane"></i>
                                                </button>
                                            @elseif($document->stage == 5)
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    Review Complete&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="status">
                                    <div class="head">Current Status</div>
                                    <div class="progress-bars">
                                        
                                        @if ($document->stage < 13)
                                            {{-- @if ($document->stage > 1)
                                                <div class="active">Initiate</div>
                                            @else
                                                <div>Initiate</div>
                                            @endif --}}
                                            @if ($document->stage > 2)
                                                <div class="active">Reviewer's Review </div>
                                            @else
                                                <div>Reviewer's Review </div>
                                            @endif
                                            @if ($stagereview)
                                                @if ($stagereview->stage == 'Review-Submit' AND $document->stage >= 5)
                                                    <div class="active">Pending Approval</div>
                                                @else
                                                    <div>Pending Approval</div>
                                                @endif
                                            @else
                                                <div>Pending Approval</div>
                                            @endif
                                        @else 
                                            <div class="bg-danger rounded-pill text-white">{{ Helpers::getDocStatusByStage($document->stage) }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array(Auth::user()->id, explode(",", $document->approvers)) && $document->stage == 6)
                        <div class="col-8">
                            <div class="inner-block tracker">
                                <div class="d-flex justify-content-between align-items-center hods">
                                    <div class="main-title">
                                        Record Workflow
                                    </div>
                                    <div class="buttons"> 
                                        @if (empty($approval_reject))
                                            @if ($stageapprove && empty($stageapprove_submit))
                                                @if($document->stage < 7)
                                                <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                    More Info Required&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    Send to Author&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                @endif
                                            @endif
                                        @endif

                                        @if (empty($stageapprove) && $document->stage == 6)
                                            @if (empty($approval_reject))
                                                <button data-bs-toggle="modal" data-bs-target="#review-cancel">
                                                       More Info Required &nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#sendtodraft">
                                                    Send to Author&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button> --}}
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    Approved&nbsp;<i class="fa-regular fa-paper-plane"></i>
                                                </button>
                                            @elseif($document->stage == 6)
                                                <button data-bs-toggle="modal" data-bs-target="#review-sign">
                                                    Approved&nbsp;<i class="fa-regular fa-circle-xmark"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="status">
                                    <div class="head">Current Status</div>
                                    <div class="progress-bars">
                                        
                                        @if ($document->stage < 13)
                                            {{-- @if ($document->stage > 1)
                                                <div class="active">Initiate</div>
                                            @else
                                                <div>Initiate</div>
                                            @endif --}}
                                            {{-- @if ($document->stage > 2)
                                                <div class="active">Pending Draft Creation</div>
                                            @else
                                                <div>Pending Draft Creation</div>
                                            @endif --}}
                                            @if ($stageapprove)
                                                @if ($stageapprove->stage == 'Approval-Submit' AND $document->stage >= 6)
                                                    <div class="active">Approval Pending</div>
                                                @else
                                                    <div>Approval Pending</div>
                                                @endif
                                            @else
                                                <div>Approval Pending</div>
                                            @endif
                                        @else 
                                            <div class="bg-danger rounded-pill text-white">{{ Helpers::getDocStatusByStage($document->stage) }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-4">
                        <div>
                            <div class="inner-block person-table">
                                <div class="main-title mb-0">
                                    Reviewers
                                </div>
                                <button data-bs-toggle="modal" data-bs-target="#doc-reviewers">
                                    View
                                </button>
                            </div>
                            <div class="inner-block person-table">
                                <div class="main-title mb-0">
                                    Approvers
                                </div>
                                <button data-bs-toggle="modal" data-bs-target="#doc-approvers">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="inner-block doc-overview">
                            <div class="main-title">Preview</div>
                            <iframe id="theFrame" width="100%" height="800"
                                src="{{ url('documents/viewpdf/' . $document->id) }}#toolbar=0"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-lg" id="doc-reviewers">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Reviewers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    @if ($document->reviewers)
                        <div class="reviewer-table table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Reviewers</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Audit Trial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rev_data = explode(',', $document->reviewers);
                                        $i = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($rev_data); $i++)
                                        @php
                                            $user = DB::table('users')
                                                ->where('id', $rev_data[$i])
                                                ->first();
                                                $user->department = DB::table('departments')
                                                ->where('id', $user->departmentid)
                                                ->value('name');
                                                $user->status = DB::table('stage_manages')
                                                ->where('user_id', $rev_data[$i])
                                                ->where('document_id', $document->id)
                                                ->where('stage', 'Reviewed')
                                                ->where('deleted_at', null)
                                                ->latest()
                                                ->first();
                                            $user->reject = DB::table('stage_manages')
                                                ->where('user_id', $rev_data[$i])
                                                ->where('document_id', $document->id)
                                                ->where('stage', 'Cancel-by-Reviewer')
                                                ->where('deleted_at', null)
                                                ->latest()
                                                ->first();

                                        @endphp
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->department }}</td>
                                            @if ($user->status)
                                                <td>Reviewed <i class="fa-solid fa-circle-check text-success"></i></td>
                                            @elseif($user->reject)
                                                <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                            @else
                                                <td>Review Pending</td>
                                            @endif
                                            <td><a
                                                href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button
                                                    type="button">Audit Trial</button></a></td>
                                        </tr>
                                    @endfor

                                </tbody>

                            </table>
                        </div>
                    @endif
                    @if ($document->reviewers_group)
                        <div class="modal-header">
                            <h4 class="modal-title">Reviewer Group</h4>
                        </div>

                        <div class="reviewer-table table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Groups</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rev_data = explode(',', $document->reviewers_group);
                                        $i = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($rev_data); $i++)
                                        @php
                                            $user = DB::table('group_permissions')
                                                ->where('id', $rev_data[$i])
                                                ->first();
                                            $user->department = DB::table('role_groups')
                                                ->where('id', $user->role_id)
                                                ->value('name');
                                            $users = explode(',', $user->user_ids);

                                            $j = 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>{{ $user->name }}</div>
                                                @if (count($users) > 1)
                                                    <ul>
                                                        @for ($j = 0; $j < count($users); $j++)
                                                            @php
                                                                $userdata = DB::table('users')
                                                                    ->where('id', $users[$j])
                                                                    ->first();

                                                                $userdata->department = DB::table('departments')
                                                                    ->where('id', $userdata->departmentid)
                                                                    ->value('name');
                                                                $userdata->approval = DB::table('stage_manages')
                                                                    ->where('document_id', $document->id)
                                                                    ->where('user_id', $users[$j])
                                                                    ->latest()
                                                                    ->first();
                                                            @endphp
                                                            <li><small>{{ $userdata->name }}</small></li>
                                                        @endfor

                                                    </ul>
                                                @endif
                                            </td>

                                            <td>{{ $user->department }}
                                                @if (count($users) > 1)
                                                    <ul>
                                                        @for ($j = 0; $j < count($users); $j++)
                                                            @php
                                                                $userdata = DB::table('users')
                                                                    ->where('id', $users[$j])
                                                                    ->first();

                                                                $userdata->department = DB::table('departments')
                                                                    ->where('id', $userdata->departmentid)
                                                                    ->value('name');
                                                                $userdata->approval = DB::table('stage_manages')
                                                                    ->where('document_id', $document->id)
                                                                    ->where('user_id', $users[$j])
                                                                    ->latest()
                                                                    ->first();
                                                            @endphp
                                                            <li><small>{{ $userdata->department }}</small></li>
                                                        @endfor

                                                    </ul>
                                                @endif
                                            </td>
                                            @if ($document->stage >= 3)
                                                <td>Reviewed <i class="fa-solid fa-circle-check text-success"></i>
                                                    @if (count($users) > 1)
                                                        <ul>
                                                            @for ($j = 0; $j < count($users); $j++)
                                                                @php
                                                                    $userdata = DB::table('users')
                                                                        ->where('id', $users[$j])
                                                                        ->first();

                                                                    $userdata->department = DB::table('departments')
                                                                        ->where('id', $userdata->departmentid)
                                                                        ->value('name');
                                                                        $user->status = DB::table('stage_manages')
                                                                        ->where('user_id', $rev_data[$i])
                                                                        ->where('document_id', $document->id)
                                                                        ->where('stage', 'Review-submit')
                                                                        ->where('deleted_at', null)
                                                                        ->latest()
                                                                        ->first();
                                                                    $user->reject = DB::table('stage_manages')
                                                                        ->where('user_id', $rev_data[$i])
                                                                        ->where('document_id', $document->id)
                                                                        ->where('stage', 'Cancel-by-Reviewer')
                                                                        ->where('deleted_at', null)
                                                                        ->latest()
                                                                        ->first();

                                                                @endphp
                                                                @if ($userdata->approval)
                                                                    <li><small>Reviewed <i
                                                                                class="fa-solid fa-circle-check text-success"></i></small>
                                                                    </li>
                                                                @elseif($userdata->reject)
                                                                    <li><small>Rejected  <i
                                                                                class="fa-solid fa-circle-xmark text-danger"></i></small>
                                                                    </li>
                                                                @else
                                                <td>Review Pending</td>
                                            @endif
                                    @endfor

                                    </ul>
                    @endif
                    </td>
                @else
                    <td>Review Pending</td>
                    @endif
                    </tr>
                    @endfor

                    </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                {{-- <button>Update</button> --}}
                <button type="button" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    </div>

    <div class="modal fade modal-lg" id="doc-approvers">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Approvers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    @if ($document->approvers)
                        <div class="reviewer-table table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Approvers</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Audit Trial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rev_data = explode(',', $document->approvers);
                                        $i = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($rev_data); $i++)
                                        @php
                                            $user = DB::table('users')
                                                ->where('id', $rev_data[$i])
                                                ->first();
                                            $user->department = DB::table('departments')
                                                ->where('id', $user->departmentid)
                                                ->value('name');
                                            $user->status = DB::table('stage_manages')
                                                ->where('user_id', $rev_data[$i])
                                                ->where('stage', 'Approved')
                                                ->where('document_id', $document->id)
                                                ->where('deleted_at', null)
                                                ->latest()
                                                ->first();
                                            $user->reject = DB::table('stage_manages')
                                                ->where('user_id', $rev_data[$i])
                                                ->where('document_id', $document->id)
                                                ->where('stage', 'Cancel-by-Approver')
                                                ->where('deleted_at', null)
                                                ->latest()
                                                ->first();

                                        @endphp
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->department }}</td>
                                            @if (@$user->status->stage=='Review-submit' || @$user->status->stage=='Approved' ||  @$user->status->stage=='Approval-Submit')
                                                <td>Approved <i class="fa-solid fa-circle-check text-success"></i></td>
                                            @elseif($user->reject)
                                                <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                            @else
                                                <td>Approval Pending</td>
                                            @endif
                                            <td><a
                                                href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button
                                                    type="button">Audit Trial</button></a></td>
                                        </tr>
                                    @endfor

                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if ($document->approver_group)
                        <div class="modal-header">
                            <h4 class="modal-title">Approvers Group</h4>
                        </div>

                        <div class="reviewer-table table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Groups</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rev_data = explode(',', $document->approver_group);
                                        $i = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($rev_data); $i++)
                                        @php
                                            $user = DB::table('group_permissions')
                                                ->where('id', $rev_data[$i])
                                                ->first();
                                            $user->department = DB::table('role_groups')
                                                ->where('id', $user->role_id)
                                                ->value('name');
                                            $users = explode(',', $user->user_ids);

                                            $j = 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>{{ $user->name }}</div>
                                                @if (count($users) > 1)
                                                    <ul>
                                                        @for ($j = 0; $j < count($users); $j++)
                                                            @php
                                                                $userdata = DB::table('users')
                                                                    ->where('id', $users[$j])
                                                                    ->first();

                                                                $userdata->department = DB::table('departments')
                                                                    ->where('id', $userdata->departmentid)
                                                                    ->value('name');
                                                                $userdata->approval = DB::table('stage_manages')
                                                                    ->where('document_id', $document->id)
                                                                    ->where('user_id', $users[$j])
                                                                    ->latest()
                                                                    ->first();
                                                            @endphp
                                                            <li><small>{{ $userdata->name }}</small></li>
                                                        @endfor

                                                    </ul>
                                                @endif
                                            </td>

                                            <td>{{ $user->department }}
                                                @if (count($users) > 1)
                                                    <ul>
                                                        @for ($j = 0; $j < count($users); $j++)
                                                            @php
                                                                $userdata = DB::table('users')
                                                                    ->where('id', $users[$j])
                                                                    ->first();

                                                                $userdata->department = DB::table('departments')
                                                                    ->where('id', $userdata->departmentid)
                                                                    ->value('name');
                                                                $userdata->approval = DB::table('stage_manages')
                                                                    ->where('document_id', $document->id)
                                                                    ->where('user_id', $users[$j])
                                                                    ->latest()
                                                                    ->first();
                                                            @endphp
                                                            <li><small>{{ $userdata->department }}</small></li>
                                                        @endfor

                                                    </ul>
                                                @endif
                                            </td>
                                            @if ($document->stage >= 3)
                                                <td>Reviewed <i class="fa-solid fa-circle-check text-success"></i>
                                                    @if (count($users) > 1)
                                                        <ul>
                                                            @for ($j = 0; $j < count($users); $j++)
                                                                @php
                                                                    $userdata = DB::table('users')
                                                                        ->where('id', $users[$j])
                                                                        ->first();

                                                                    $userdata->department = DB::table('departments')
                                                                        ->where('id', $userdata->departmentid)
                                                                        ->value('name');
                                                                        $user->status = DB::table('stage_manages')
                                                                        ->where('user_id', $rev_data[$i])
                                                                        ->where('document_id', $document->id)
                                                                        ->where('stage', 'Review-submit')
                                                                        ->where('deleted_at', null)
                                                                        ->latest()
                                                                        ->first();
                                                                    $user->reject = DB::table('stage_manages')
                                                                        ->where('user_id', $rev_data[$i])
                                                                        ->where('document_id', $document->id)
                                                                        ->where('stage', 'Cancel-by-Reviewer')
                                                                        ->where('deleted_at', null)
                                                                        ->latest()
                                                                        ->first();

                                                                @endphp
                                                                @if ($userdata->approval)
                                                                    <li><small>Approved <i
                                                                                class="fa-solid fa-circle-check text-success"></i></small>
                                                                    </li>
                                                                @elseif($userdata->reject)
                                                                    <li><small>Rejected  <i
                                                                                class="fa-solid fa-circle-xmark text-danger"></i></small>
                                                                    </li>
                                                                @else
                                                <td>Approval Pending</td>
                                            @endif
                                    @endfor

                                    </ul>
                    @endif
                    </td>
                @else
                    <td>Approval Pending</td>
                    @endif
                    </tr>
                    @endfor

                    </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                {{-- <button>Update</button> --}}
                <button type="button" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    </div>

    <div class="modal fade" id="review-sign">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">E-Signature</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form action="{{ url('sendforstagechanage') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
                        </div>
                        <div class="group-input">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('username') }}" name="username" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">User name not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" value="{{ old('password') }}" name="password" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">E-signature not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="comment">Comment <span class="text-danger">*</span></label>
                            <textarea required name="comment" value="{{ old('comment') }}"></textarea>
                        </div>

                        @php
                            $hideSubmitButton = false;
                        @endphp

                        @if ($document->stage == 2 && empty($document->drafter_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the Drafter input are completed before proceeding with the activity to send it for HOD review.</div>
                            @php $hideSubmitButton = true; @endphp
                        @elseif ($document->stage == 3 && empty($document->hod_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the HOD input are completed before proceeding with the activity to HOD Review Complete.</div>
                            @php $hideSubmitButton = true; @endphp
                        @elseif ($document->stage == 4 && empty($document->qa_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the QA input are completed before proceeding with the activity to QA Review Complete.</div>
                            @php $hideSubmitButton = true; @endphp
                        @elseif ($document->stage == 5 && empty($document->reviewer_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the Reviewer input are completed before proceeding with the activity to Reviewer Review.</div>
                            @php $hideSubmitButton = true; @endphp
                        @elseif ($document->stage == 6 && empty($document->approver_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the Approver input are completed before proceeding with the activity to Approved.</div>
                            @php $hideSubmitButton = true; @endphp
                        @endif
                    </div>

                    @if ($document->stage == 2)
                        <input type="hidden" name="stage_id" value="Draft Review Submit" />
                    @endif
                    @if ($drafter)
                        @if ($drafter->stage == 'Draft Review Submit')
                            <input type="hidden" name="stage_id" value="Draft Review Complete" />
                        @endif
                    @endif

                    @if ($document->stage == 3)
                        <input type="hidden" name="stage_id" value="HOD Review Submit" />
                    @endif
                    @if ($stagehod)
                        @if ($stagehod->stage == 'HOD Review Submit')
                            <input type="hidden" name="stage_id" value="HOD Review Complete" />
                        @endif
                    @endif

                    @if ($document->stage == 4)
                        <input type="hidden" name="stage_id" value="QA Review Submit" />
                    @endif
                    @if ($stageqa)
                        @if ($stageqa->stage == 'QA Review Submit')
                            <input type="hidden" name="stage_id" value="QA Review Complete" />
                        @endif
                    @endif

                    @if ($document->stage == 5)
                        <input type="hidden" name="stage_id" value="Reviewed" />
                    @endif
                    @if ($stagereview)
                        @if ($stagereview->stage == 'Reviewed')
                            <input type="hidden" name="stage_id" value="Review-Submit" />
                        @endif
                    @endif

                    @if ($document->stage == 6)
                        <input type="hidden" name="stage_id" value="Approved" />
                    @endif
                    @if ($stageapprove)
                        @if ($stageapprove->stage == 'Approved')
                            <input type="hidden" name="stage_id" value="Approval-Submit" />
                        @endif
                    @endif

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        {{-- @if (!$hideSubmitButton)
                            <button type="submit">Submit</button>
                        @endif --}}
                        <button type="submit">Submit</button>
                        <button type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="review-cancel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">E-Signature</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form action="{{ url('sendforstagechanage') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
                        </div>
                        <div class="group-input">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('username') }}" name="username" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">User name not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" value="{{ old('password') }}" name="password" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">E-signature not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="comment">Comment <span class="text-danger">*</span></label>
                            <textarea required name="comment" value="{{ old('comment') }}"></textarea>
                        </div> 
                    </div>
                    @if (Helpers::checkRoles(40) AND Helpers::checkRoles_check_draft($document) && $document->stage == 2)
                        <input type="hidden" name="stage_id" value="Cancel-by-Drafter" />
                    @endif
                    @if (Helpers::checkRoles(4) AND Helpers::checkRoles_check_hods($document) && $document->stage == 3)
                        <input type="hidden" name="stage_id" value="Cancel-by-HOD" />
                    @endif
                    @if ($document->stage == 4)
                        <input type="hidden" name="stage_id" value="Cancel-by-QA" />
                    @endif
                    @if ($document->stage == 5)
                        <input type="hidden" name="stage_id" value="Cancel-by-Reviewer" />
                    @endif
                    @if ($document->stage == 6)
                        <input type="hidden" name="stage_id" value="Cancel-by-Approver" />
                    @endif

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit">Submit</button>
                        <button type="button" data-bs-dismiss="modal">Close</button>
                        {{-- <button>Close</button> --}}
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="sendtodraft">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">E-Signature</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form action="{{ url('sendfordraft') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
                        </div>
                        <div class="group-input">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('username') }}" name="username" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">User name not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" value="{{ old('password') }}" name="password" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">E-signature not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="comment">Comment <span class="text-danger">*</span></label>
                            <textarea required name="comment" value="{{ old('comment') }}"></textarea>
                        </div> 
                    </div>
                    @if ($document->stage == 2)
                        <input type="hidden" name="stage_id" value="1" />
                    @endif
                    @if ($document->stage == 3)
                        <input type="hidden" name="stage_id" value="2" />
                    @endif
                    @if ($document->stage == 4)
                        <input type="hidden" name="stage_id" value="3" />
                    @endif
                    @if ($document->stage == 5)
                        <input type="hidden" name="stage_id" value="4" />
                    @endif
                    @if ($document->stage == 6)
                        <input type="hidden" name="stage_id" value="5" />
                    @endif


                        {{-- <input type="hidden" name="stage_id" value="2" /> --}}
                        {{-- <input type="hidden" name="status" value={{$document->status}} /> --}}

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit">Submit</button>
                        <button type="button" data-bs-dismiss="modal">Close</button>
                        {{-- <button>Close</button> --}}
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="cancel-record">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">E-Signature</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form action="{{ url('sendforstagechanage') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
                        </div>
                        <div class="group-input">
                            <label for="username">Username</label>
                            <input type="text" value="{{ old('username') }}" name="username" class="form-control" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">User name not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="password">Password</label>
                            <input type="password" value="{{ old('password') }}" name="password" class="form-control" required>
                            @if ($errors->has('username'))
                                <p class="text-danger">E-signature not matched</p>
                            @endif
                        </div>
                        <div class="group-input">
                            <label for="comment">Comment<span class="text-danger">*</span></label>
                            <textarea required name="comment" value="{{ old('comment') }}" class="form-control"></textarea>
                        </div> 
                    </div>
                    @if (Helpers::checkRoles(4))
                        <input type="hidden" name="stage_id" value="Close-by-HOD" />
                    @endif

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit">Submit</button>
                        <button type="button" data-bs-dismiss="modal">Close</button>
                        {{-- <button>Close</button> --}}
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
