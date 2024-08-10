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

                                <button onclick="location.href='{{ route('download.word', $document->id) }}';">
                                    Download as Word
                                </button>
                                {{-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#print-modal1">
                                    Print Document Word
                                </button> --}}

                                @if ($document->revised == 'Yes')
                                <button onclick="location.href='{{ route('document.revision.history', $document->id) }}';">
                                    Revision History
                                </button>
                                @endif

                                <button onclick="location.href='{{ url('notification', $document->id) }}';">
                                    Send Notification
                                </button>

                                <button onclick="location.href='{{ url('audit-trial', $document->id) }}';">
                                    Audit Trail
                                </button>
                                @if ($document->status !== 'Obsolete')
                                <button onclick="location.href='{{ route('documents.edit', $document->id) }}';">Edit </button>
                                {{-- <button>Cancel</button> --}}
                                @endif
                                {{-- <button onclick="location.href='{{ url('documents/generatePdf', $document->id) }}';">Download
                                </button> --}}

                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#print-modal1">
                                    Download
                                </button>

                                {{-- <button onclick="location.href='{{ url('documents/printPDF', $document->id) }}';" target="__blank"> --}}
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#print-modal">
                                    Print
                                </button>
                                {{-- @if ($document->stage >= 7)
                                        <button data-bs-toggle="modal" data-bs-target="#child-modal">Child</button>
                                    @endif --}}
                                @if ($document->stage >= 10 && $document->status !== 'Obsolete')
                                {{-- <button type="button" class="btn btn-danger" id="obsolete-button">Obsolete</button> --}}
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signature-modal">
                                    Obsolete
                                </button>
                                {{-- <button>Obsolete</button> --}}
                                <button data-bs-toggle="modal" data-bs-target="#child-modal">Revise</button>
                                @endif

                            </div>
                        </div>
                        <div class="bottom-block">
                            <div>
                                <div class="head">Document Number</div>
                                <div>
                                    @if($document->revised === 'Yes')
                                    000{{ $document->revised_doc }}
                                    @else
                                    000{{ $document->id }}
                                    @endif
                                </div>
                            </div>
                            {{-- <div>
                                    <div class="head">Department</div>
                                    <div>{{ $document->department_name->name }}
                        </div>
                    </div> --}}
                    <div>
                        <div class="head">Document Type</div>
                        <div>{{ $document->document_type_id }}</div>
                    </div>
                    <div>
                        <div class="head">Working Status</div>
                        <div>{{ Helpers::getDocStatusByStage($document->stage, $document->training_required) }}</div>
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
                        <div>{{ \Carbon\Carbon::parse($document->last_modify_date->created_at)->format('d-M-Y H:i A') }}</div>
                        @else
                        <div>{{ \Carbon\Carbon::parse($document->created_at)->format('d-M-Y H:i A') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="inner-block tracker">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="main-title">
                        Record Workflow
                    </div>

                    @if ($document->stage == 1)
                    <input type="hidden" name="stage_id" value="2" />
                    <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#approve-sign">
                        Send For Author<i class="fa-regular fa-paper-plane"></i>
                    </button>
                    @endif

                    @if ($document->training_required == 'yes')
                    @if ($document->stage == 7)
                    <input type="hidden" name="stage_id" value="8" />
                    <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#approve-sign">
                        Send For Training<i class="fa-regular fa-paper-plane"></i>
                    </button>
                    @endif
                    @endif


                </div>
                <div class="status">
                    <div class="head">Current Status</div>
                    @if ($document->stage < 13) <div class="progress-bars">
                        @if ($document->stage >= 1)
                        <div class="active">Initiate</div>
                        @else
                        <div class="">Initiate</div>
                        @endif
                        @if ($document->stage >= 2)
                        <div class="active">Pending Draft Creation</div>
                        @else
                        <div class="">Pending Draft Creation</div>
                        @endif
                        @if ($document->stage >= 3)
                        <div class="active">HOD Review</div>
                        @else
                        <div class="">HOD Review</div>
                        @endif
                        @if ($document->stage >= 4)
                        <div class="active">QA Review</div>
                        @else
                        <div class="">QA Review</div>
                        @endif
                        @if ($document->stage >= 5)
                        <div class="active">Reviewer Review</div>
                        @else
                        <div class="">Reviewer Review</div>
                        @endif
                        @if ($document->stage >= 6)
                        <div class="active">Approver Pending</div>
                        @else
                        <div class="">Approver Pending</div>
                        @endif
                        @if ($document->training_required == 'yes')
                        @if ($document->stage >= 7)
                        <div class="active">Pending-Traning</div>
                        @else
                        <div class="">Pending-Traning</div>
                        @endif
                        @if ($document->stage >= 8)
                        <div class="active">Traning Started</div>
                        @else
                        <div class="">Traning Started</div>
                        @endif
                        @if ($document->stage >= 9)
                        <div class="active">Traning-Complete</div>
                        @else
                        <div class="">Traning-Complete</div>
                        @endif
                        @endif
                        @if ($document->stage >= 10)
                        <div class="active">Effective</div>
                        @else
                        <div class="">Effective</div>
                        @endif
                        @if ($document->stage == 11)
                        <div class="active">Obsolete</div>
                        @else
                        <div class="">Obsolete</div>
                        @endif
                </div>
                @else
                <div class="bg-danger text-white rounded-pill text-center">
                    {{ Helpers::getDocStatusByStage($document->stage) }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-between text-center">

            <div class="col-2">
                <div class="inner-block person-table">
                    <div class="main-title mb-0">
                        Authors
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doc-drafter">
                        View
                    </button>
                </div>
            </div>
            <div class="col-2">
                <div class="inner-block person-table">
                    <div class="main-title mb-0">
                        HODs
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doc-hods">
                        View
                    </button>
                </div>
            </div>
            <div class="col-2">
                <div class="inner-block person-table">
                    <div class="main-title mb-0">
                        QAs
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doc-qa">
                        View
                    </button>
                </div>
            </div>
            <div class="col-2">
                <div class="inner-block person-table">
                    <div class="main-title mb-0">
                        Reviewers
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doc-reviewers">
                        View
                    </button>
                </div>
            </div>
            <div class="col-2">
                <div class="inner-block person-table">
                    <div class="main-title mb-0">
                        Approvers
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doc-approvers">
                        View
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12">
        <div class="inner-block doc-overview">
            <div class="main-title d-flex justify-content-between pa-5">
                <div>Preview</div>
                <div>
                    {{-- <button class="btn btn-primary rounded-pill">Issue Copies</button> --}}
                </div>
            </div>

            <iframe id="theFrame" width="100%" height="800" src="{{ url('documents/viewpdf/' . $document->id) }}#toolbar=0"></iframe>
        </div>
    </div>

</div>
</div>
</div>
</div>

<div class="modal fade modal-lg" id="doc-hods">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">HODs</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->

            <div class="modal-body">
                <div class="reviewer-table table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>HODs</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $hod_data = explode(',', $document->hods);
                            $i = 0;
                            @endphp
                            @for ($i = 0; $i < count($hod_data); $i++) @php $user=DB::table('users') ->where('id', $hod_data[$i])
                                ->first();
                                $user->department = DB::table('departments')
                                ->where('id', $user->departmentid)
                                ->value('name');
                                $user->status = DB::table('stage_manages')
                                ->where('user_id', $hod_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'HOD Review Complete')
                                ->where('deleted_at', null)
                                ->latest()
                                ->first();
                                $user->statusReject = DB::table('stage_manages')
                                ->where('user_id', $hod_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'Cancel-by-HOD')
                                ->where('deleted_at', null)
                                ->latest()
                                ->first();
                                @endphp
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->department }}</td>
                                    @if ($user->status)
                                    <td>HOD Review Complete <i class="fa-solid fa-circle-check text-success"></i></td>
                                    @elseif($user->statusReject)
                                    <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                    @else
                                    <td>HOD Review Pending</td>
                                    @endif
                                    {{-- <td><a
                                                href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit Trial</button></a></td> --}}
                                </tr>
                                @endfor

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-lg" id="doc-drafter">
    <form action="{{ route('update-doc', $document->id) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Authors</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->

                <div class="modal-body">
                    @if ($document->stage <= 2) <div class="add-reviewer">
                        <select id="choices-multiple-remove-button" name="reviewers[]" placeholder="Select Reviewers" multiple>
                            @if (!empty($drafter))
                            @foreach ($drafter as $lan)
                            <option value="{{ $lan->id }}">
                                @if ($document->drafters)
                                @php
                                $data = explode(',', $document->drafters);
                                $count = count($data);
                                $i = 0;
                                @endphp
                                @for ($i = 0; $i < $count; $i++) @if ($data[$i]==$lan->id)
                                    selected
                                    @endif
                                    @endfor
                                    @endif>
                                    {{ $lan->name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                </div>
                @endif
                <div class="reviewer-table table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Authors</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Audit Trial</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $rev_data = explode(',', $document->drafters);
                            $i = 0;
                            @endphp
                            @for ($i = 0; $i < count($rev_data); $i++) @php $user=DB::table('users') ->where('id', $rev_data[$i])
                                ->first();
                                $user->department = DB::table('departments')
                                ->where('id', $user->departmentid)
                                ->value('name');
                                $user->status = DB::table('stage_manages')
                                ->where('user_id', $rev_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'Draft Review Complete')
                                ->where('deleted_at', null)
                                ->latest()
                                ->first();
                                $user->statusReject = DB::table('stage_manages')
                                ->where('user_id', $rev_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'Cancel-by-Drafter')
                                ->where('deleted_at', null)
                                ->latest()
                                ->first();
                                @endphp
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->department }}</td>
                                    @if ($user->status)
                                    <td>Drafter Review Complete <i class="fa-solid fa-circle-check text-success"></i></td>
                                    @elseif($user->statusReject)
                                    <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                    @else
                                    <td>Drafter Review Pending</td>
                                    @endif
                                    <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit Trial</button></a></td>
                                </tr>
                                @endfor

                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                {{-- @if ($document->stage <= 2)
                    <button type="submit">Update</button>
                @endif --}}
                <button type="button" data-bs-dismiss="modal">Close</button>
            </div>
    </form>

</div>
</div>
</div>
<div class="modal fade modal-lg" id="doc-qa">
    <form action="{{ route('update-doc', $document->id) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">QAs</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->

                <div class="modal-body">
                    @if ($document->stage <= 2) <div class="add-reviewer">
                        <select id="choices-multiple-remove-button" name="reviewers[]" placeholder="Select Reviewers" multiple>
                            @if (!empty($qas))
                            @foreach ($qas as $lan)
                            <option value="{{ $lan->id }}">
                                @if ($document->qa)
                                @php
                                $data = explode(',', $document->qa);
                                $count = count($data);
                                $i = 0;
                                @endphp
                                @for ($i = 0; $i < $count; $i++) @if ($data[$i]==$lan->id)
                                    selected
                                    @endif
                                    @endfor
                                    @endif>
                                    {{ $lan->name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                </div>
                @endif
                <div class="reviewer-table table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>QAs</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Audit Trial</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $rev_data = explode(',', $document->qa);
                            $i = 0;
                            @endphp
                            @for ($i = 0; $i < count($rev_data); $i++) @php $user=DB::table('users') ->where('id', $rev_data[$i])
                                ->first();
                                $user->department = DB::table('departments')
                                ->where('id', $user->departmentid)
                                ->value('name');
                                $user->status = DB::table('stage_manages')
                                ->where('user_id', $rev_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'QA Review Complete')
                                ->where('deleted_at', null)
                                ->latest()
                                ->first();
                                $user->statusReject = DB::table('stage_manages')
                                ->where('user_id', $rev_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'Cancel-by-QA')
                                ->where('deleted_at', null)
                                ->latest()
                                ->first();
                                @endphp
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->department }}</td>
                                    @if ($user->status)
                                    <td>QA Review Complete <i class="fa-solid fa-circle-check text-success"></i></td>
                                    @elseif($user->statusReject)
                                    <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                    @else
                                    <td>QA Review Pending</td>
                                    @endif
                                    <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit Trial</button></a></td>
                                </tr>
                                @endfor

                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                {{-- @if ($document->stage <= 2)
                    <button type="submit">Update</button>
                @endif --}}
                <button type="button" data-bs-dismiss="modal">Close</button>
            </div>
    </form>

</div>
</div>
</div>
<div class="modal fade modal-lg" id="doc-reviewers">
    <form action="{{ route('update-doc', $document->id) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Reviewers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->

                <div class="modal-body">
                    @if ($document->stage <= 2) <div class="add-reviewer">
                        <select id="choices-multiple-remove-button" name="reviewers[]" placeholder="Select Reviewers" multiple>
                            @if (!empty($reviewer))
                            @foreach ($reviewer as $lan)
                            <option value="{{ $lan->id }}">
                                @if ($document->reviewers)
                                @php
                                $data = explode(',', $document->reviewers);
                                $count = count($data);
                                $i = 0;
                                @endphp
                                @for ($i = 0; $i < $count; $i++) @if ($data[$i]==$lan->id)
                                    selected
                                    @endif
                                    @endfor
                                    @endif>
                                    {{ $lan->name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                </div>
                @endif
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
                            @for ($i = 0; $i < count($rev_data); $i++) @php $user=DB::table('users') ->where('id', $rev_data[$i])
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
                                $user->statusReject = DB::table('stage_manages')
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
                                    @elseif($user->statusReject)
                                    <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                    @else
                                    <td>Review Pending</td>
                                    @endif
                                    <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit Trial</button></a></td>
                                </tr>
                                @endfor

                        </tbody>
                    </table>
                </div>
                <div class="modal-header">
                    <h4 class="modal-title">Reviewer Group</h4>
                </div>

                @if ($document->stage <= 2) <div class="add-reviewer">
                    <select id="choices-multiple-remove-button" name="reviewers_group[]" placeholder="Select Reviewers" multiple>
                        @if (!empty($reviewergroup))
                        @foreach ($reviewergroup as $lan)
                        <option value="{{ $lan->id }}">
                            @if ($document->reviewers_group)
                            @php
                            $data = explode(',', $document->reviewers_group);
                            $count = count($data);
                            $i = 0;
                            @endphp
                            @for ($i = 0; $i < $count; $i++) @if ($data[$i]==$lan->id)
                                selected
                                @endif
                                @endfor
                                @endif>
                                {{ $lan->name }}
                        </option>
                        @endforeach
                        @endif
                    </select>
            </div>
            @endif
            @if ($document->reviewers_group)
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
                        @for ($i = 0; $i < count($rev_data); $i++) @php $user=DB::table('group_permissions') ->where('id', $rev_data[$i])
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
                                    {{-- @if (count($users) > 0)
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
                                    @endif --}}
                                </td>

                                <td>{{ $user->department }}
                                    @if (count($users) > 1)
                                    <ul>
                                        @for ($j = 0; $j < count($users); $j++) @php $userdata=DB::table('users') ->where('id', $users[$j])
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
                                        @for ($j = 0; $j < count($users); $j++) @php $userdata=DB::table('users') ->where('id', $users[$j])
                                            ->first();

                                            $userdata->department = DB::table('departments')
                                            ->where('id', $userdata->departmentid)
                                            ->value('name');
                                            $userdata->approval = DB::table('stage_manages')
                                            ->where('document_id', $document->id)
                                            ->where('user_id', $users[$j])
                                            ->where('stage', 'Review-Submit')
                                            ->where('deleted_at', null)
                                            ->latest()
                                            ->first();
                                            $userdata->reject = DB::table('stage_manages')
                                            ->where('document_id', $document->id)
                                            ->where('user_id', $users[$j])
                                            ->where('stage', 'Cancel-by-reviewer')
                                            ->where('deleted_at', null)
                                            ->latest()
                                            ->first();

                                            @endphp
                                            @if ($userdata->approval)
                                            <li><small>Reviewed <i class="fa-solid fa-circle-check text-success"></i></small>
                                            </li>
                                            @elseif($userdata->reject)
                                            <li><small>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></small>
                                            </li>
                                            @else
                                <td>Review Pending</td>
                                <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit</button></a></td>

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
            {{-- @if ($document->stage <= 2)
                        <button type="submit">Update</button>
                    @endif --}}
            <button type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </form>

</div>
</div>
</div>


<div class="modal fade modal-lg" id="doc-approvers">
    <form action="{{ route('update-doc', $document->id) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Approvers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    @if ($document->stage <= 4) <div class="add-reviewer">
                        <select id="choices-multiple-remove-button" name="approvers[]" placeholder="Select Reviewers" multiple>
                            @if (!empty($approvers))
                            @foreach ($approvers as $lan)
                            <option value="{{ $lan->id }}" @if ($document->reviewers_group) @php
                                $data = explode(",",$document->approvers);
                                $count = count($data);
                                $i=0;
                                @endphp
                                @for ($i = 0; $i < $count; $i++) @if ($data[$i]==$lan->id)
                                    selected @endif
                                    @endfor
                                    @endif>
                                    {{ $lan->name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                </div>
                @endif
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
                            @for ($i = 0; $i < count($rev_data); $i++) @php $user=DB::table('users') ->where('id', $rev_data[$i])
                                ->first();
                                $user->department = DB::table('departments')
                                ->where('id', $user->departmentid)
                                ->value('name');
                                $user->status = DB::table('stage_manages')
                                ->where('user_id', $rev_data[$i])
                                ->where('document_id', $document->id)
                                ->where('stage', 'Approval-submit')
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
                                    @if ($user->status)
                                    <td>Approved <i class="fa-solid fa-circle-check text-success"></i></td>
                                    @elseif($user->reject)
                                    <td>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></td>
                                    @else
                                    <td>Approval Pending</td>
                                    @endif
                                    <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit</button></a></td>


                                </tr>
                                @endfor

                        </tbody>
                    </table>
                </div>
                <div class="modal-header">
                    <h4 class="modal-title">Approvers Group</h4>
                </div>
                @if ($document->stage <= 4) <div class="add-reviewer">
                    <select id="choices-multiple-remove-button" name="approver_group[]" placeholder="Select Reviewers" multiple>
                        @if (!empty($approversgroup))
                        @foreach ($approversgroup as $lan)
                        <option value="{{ $lan->id }}" @if ($document->approver_group) @php
                            $data = explode(",",$document->approver_group);
                            $count = count($data);
                            $i=0;
                            @endphp
                            @for ($i = 0; $i < $count; $i++) @if ($data[$i]==$lan->id)
                                selected @endif
                                @endfor
                                @endif>
                                {{ $lan->name }}
                        </option>
                        @endforeach
                        @endif

                    </select>
            </div>
            @endif
            @if ($document->approver_group)
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
                        @for ($i = 0; $i < count($rev_data); $i++) @php $user=DB::table('group_permissions') ->where('id', $rev_data[$i])
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
                                    {{-- @if (count($users) > 0)
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
                                    @endif --}}
                                </td>

                                <td>{{ $user->department }}
                                    @if (count($users) > 1)
                                    <ul>
                                        @for ($j = 0; $j < count($users); $j++) @php $userdata=DB::table('users') ->where('id', $users[$j])
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
                                @if ($document->stage >= 5)
                                <td>Approved <i class="fa-solid fa-circle-check text-success"></i>
                                    @if (count($users) > 1)
                                    <ul>
                                        @for ($j = 0; $j < count($users); $j++) @php $userdata=DB::table('users') ->where('id', $users[$j])
                                            ->first();

                                            $userdata->department = DB::table('departments')
                                            ->where('id', $userdata->departmentid)
                                            ->value('name');
                                            $userdata->approval = DB::table('stage_manages')
                                            ->where('document_id', $document->id)
                                            ->where('user_id', $users[$j])
                                            ->where('stage', 'Approval-Submit')
                                            ->where('deleted_at', null)
                                            ->latest()
                                            ->first();
                                            $userdata->reject = DB::table('stage_manages')
                                            ->where('document_id', $document->id)
                                            ->where('user_id', $users[$j])
                                            ->where('stage', 'Cancel-by-approver')
                                            ->where('deleted_at', null)
                                            ->latest()
                                            ->first();

                                            @endphp
                                            @if ($userdata->approval)
                                            <li><small>Approved <i class="fa-solid fa-circle-check text-success"></i></small>
                                            </li>
                                            @elseif($userdata->reject)
                                            <li><small>Rejected <i class="fa-solid fa-circle-xmark text-danger"></i></small>
                                            </li>
                                            @else
                                <td>Approval Pending</td>

                                @endif
                                <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit</button></a></td>

                                @endfor

                                </ul>
                                @endif
                                </td>
                                @else
                                <td>Approval Pending</td>
                                @endif
                                <td><a href="{{ url('audit-individual/') }}/{{ $document->id }}/{{ $user->id }}"><button type="button">Audit</button></a></td>

                            </tr>
                            @endfor

                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            {{-- @if ($document->stage != 1)
            <button type="submit">Update</button>
        @endif --}}
            <button type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</div>
</div>
</div>

<div class="modal fade" id="approve-sign">
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
                        <input required name="comment" value="{{ old('comment') }}" />
                    </div>
                </div>
                @if ($document->stage == 1)
                <input type="hidden" name="stage_id" value="2" />
                @endif
                @if ($document->training_required == 'yes')
                @if ($document->stage == 7)
                <input type="hidden" name="stage_id" value="8" />
                @endif
                @else
                @endif
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- <button>Close</button> --}}
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="signature-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">E-Signature</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('sendforstagechanage') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="stage_id" value="10" />
                <input type="hidden" name="document_id" value="{{ $document->id }}">
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="mb-3 text-justify">
                        Please select a meaning and an outcome for this task and enter your username
                        and password for this task. You are performing an electronic signature,
                        which is the legally binding equivalent of a handwritten signature.
                    </div>
                    <div class="group-input">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="group-input">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="group-input">
                        <label for="comment">Comment</label>
                        <input type="comment" name="comment" required>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- updated print-model -->
<div class="modal fade" id="print-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Print Document</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('document.print.pdf', $document->id) }}" method="GET" target="_blank">
                @csrf
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="group-input mb-3">
                        <label for="print_document_title">Document Title</label>
                        <input type="text" name="document_name" class="form-control w-100" maxlength="255" value="{{$document->document_name}}" readonly>
                    </div>
                    @php
                    $doc_number = '';
                    $doc_number = Helpers::getDivisionName($document->division_id)
                    . '/' . ($document->document_type_name ? $temp . ' /' : '')
                    . $document->created_at->format('Y')
                    . '/000' . $document->id . 'R1.0';
                    @endphp

                    <div class="group-input mb-3">
                        <label for="print_document_number">Document Number</label>
                        <input type="text" name="document_number" value="{{ $doc_number }}" class="form-control w-100" maxlength="255" readonly>
                    </div>
                    @php
                    $currentUser = Auth::user();
                    @endphp
                    <div class="group-input mb-3">
                        <label for="Document_Printed_By">Document Printed By</label>
                        <input type="text" id="Document_Printed_By" name="user_name" class="form-control w-100" value="{{ $currentUser->name }}" readonly>
                        <input type="hidden" name="user_id" value="{{ $currentUser->id }}">
                        @error('document_printed_by')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="group-input mb-3">
                        <label for="issue_copies">No. Of Copies <span class="text-danger">*</span></label>
                        <input type="number" id="issue_copies" name="issue_copies" value="1" min="1" class="form-control w-100" required>
                    </div>
                    <div class="group-input mb-3">
                        <label for="print_reason">Print Reason<span class="text-danger">*</span></label>
                        <textarea name="print_reason" class="form-control w-100" maxlength="255" required></textarea>
                    </div>

                    @php
                    $users = DB::table('users')->get();
                    @endphp

                    <div class="group-input mb-3">
                        <textarea name="date" class="form-control w-100" maxlength="255" hidden>{{$document->create_at}}</textarea>
                    </div>

                    <div class="group-input new-date-data-field mb-3">
                        <label for="Number_of_Print_Copies" style="font-weight: normal;">Issuance Date</label>
                        <div class="input-date">
                            <div class="calenderauditee">
                                <input type="text" id="date' + serialNumber +'" readonly placeholder="DD-MMM-YYYY" class="form-control w-100" />
                                <input type="date" name="date" class="hide-input" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" oninput="handleDateInput(this, `date' + serialNumber +'`)" />
                            </div>
                        </div>
                    </div>

                    <div class="group-input mb-3">
                        <label for="Issued  To" style="position: relative; left: 16px;">Issued To</label>
                        <select id="select-state" placeholder="Select..." name="issuance_to" class="form-control" style="width: 95%; position: relative; left: 16px;">
                            <option value="">Select a value</option>
                            @foreach ($users as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                        @error('issuance_to')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group-input mb-3">
                        <label for="Department Location" style="position: relative; left: 16px;"> Department/Location</label>
                        <select id="select-state" placeholder="Select..." name="department" class="form-control" style="width: 95%; position: relative; left: 13px;">
                            <option value='0'>-- Select --</option>
                            <option value='1'>Calibration Lab</option>
                            <option value='2'>Engineering</option>
                            <option value='3'>Facilities</option>
                            <option value='4'>LAB</option>
                            <option value='5'>Labeling</option>
                            <option value='6'>Manufacturing</option>
                            <option value='7'>Quality Assurance</option>
                            <option value="8">Quality Control</option>
                            <option value="9"> Regulatory Affairs</option>
                            <option value="10">Security</option>
                            <option value="11">Training</option>
                            <option value="12">IT</option>
                            <option value="13">Application Engineering</option>
                            <option value="14">Trading</option>
                            <option value="15">Research</option>
                            <option value="16">Sales</option>
                            <option value="17">Finance</option>
                            <option value="18">System</option>
                            <option value="19">Administrative</option>
                            <option value="20">M&A</option>
                            <option value="21">R&D</option>
                            <option value="22">Human Resources</option>
                            <option value="23">Banking</option>
                            <option value="24">Marketing</option>
                        </select>
                    </div>

                    <div class="group-input mb-3"><label for="issued_copies" style="position: relative; left: 16px;">Number of Issued Copies</label>
                        <input type="text" id="issued_copies" name="issued_copies" class="form-control" maxlength="255" style="width: 95%; position: relative; left: 16px;">
                    </div>
                    <div class="group-input mb-3">
                        <label for="Reason_for_Issuance" style="position: relative; left: 16px;">Reason for Issuance</label>
                        <textarea name="issued_reason" class="form-control" maxlength="255" style="width: 95%; position: relative; left: 16px;"></textarea>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary rounded">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('issue_copies').addEventListener('input', function() {
        const issueCopies = document.getElementById('issue_copies').value;
        document.getElementById('issued_copies').value = issueCopies;
    });
</script>


<!--updated print-model print as word-->
<div class="modal fade" id="print-modal1">
    <div class=" modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Download Document</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('document.print.downloadpdf', $document->id) }}" method="GET" target="_blank">
                @csrf
                <!-- Modal body -->
                <div class="modal-body">

                    @php
                    $currentUser = Auth::user();
                    @endphp
                    <div class="group-input mb-3">
                        <label for="Document_Printed_By">Document Printed By</label>
                        <input type="text" id="Document_Printed_By" name="user_name" class="form-control w-100" value="{{ $currentUser->name }}" readonly>
                        <input type="hidden" name="user_id" value="{{ $currentUser->id }}">
                        @error('document_printed_by')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group-input mb-3">
                        <label for="issue_copies">No. Of Copies <span class="text-danger">*</span></label>
                        <input type="number" id="issue_copie" name="issue_copies" value="1" min="1" class="form-control w-100" required>
                    </div>

                    @php
                    $users = DB::table('users')->get();
                    @endphp
                    <div class="group-input mb-3">
                        <textarea name="date" class="form-control w-100" maxlength="255" hidden>{{$document->create_at}}</textarea>
                    </div>

                    <div class="group-input mb-3"><label for="issued_copies" style="position: relative; left: 16px;">Number of Issued Copies</label>
                        <input type="text" id="issued_copie" name="issued_copies" class="form-control" maxlength="255" style="width: 95%; position: relative; left: 16px;">
                    </div>

                    <div class="group-input mb-3">
                        <label for="print_reason">Download Reason<span class="text-danger">*</span></label>
                        <textarea name="print_reason" class="form-control w-100" maxlength="255"></textarea>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary rounded">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('issue_copie').addEventListener('input', function() {
        const issueCopies = document.getElementById('issue_copie').value;
        document.getElementById('issued_copie').value = issueCopies;
    });
</script>


<div class="modal fade" id="child-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" style="font-weight: 900">Document Revision</h4>
            </div>
            @if($document->revised === 'Yes')

            <form method="POST" action="{{ url('revision',$document->revised_doc) }}">
                @else
                <form method="POST" action="{{ url('revision',$document->id) }}">


                    @endif

                    @csrf
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="group-input">
                            <label for="revision">Choose Revision Version</label>
                            <label for="major">
                                Major Version<span class="text-primary" data-bs-toggle="modal" data-bs-target="#document-management-system-modal" style="font-size: 0.8rem; font-weight: 400;">
                                    (Launch Instruction)
                                </span>
                            </label>
                            <input type="number" name="major" id="major" min="0">
                            {{-- <option value="0">-- Select --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select> --}}

                            <label for="minor">
                                {{-- <input type="radio" name="revision" id="minor"> --}}
                                Minor Version<span class="text-primary" data-bs-toggle="modal" data-bs-target="#document-management-system-modal-minor" style="font-size: 0.8rem; font-weight: 400;">
                                    (Launch Instruction)
                                </span>
                            </label>
                            <input type="number" name="minor" id="minor" min="0" max="9">
                            {{-- <option value="">-- Select --</option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select> --}}


                            <label for="reason">
                                Comment
                            </label>
                            <input type="text" name="reason" required>
                        </div>

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" data-bs-dismiss="modal">Close</button>
                        <button type="submit">Submit</button>
                    </div>
                </form>

        </div>
    </div>
</div>

{{-- CHAT PDF MODAL START --}}
<button style="position: sticky; bottom: 1rem; left: 95%;" type="button" class="btn bg-theme rounded-circle shadow-lg" data-bs-toggle="modal" data-bs-target="#chatModal">
    <!--<img src="{{ asset('user/images/ai_2814666.png') }}" alt="chatWithDoc" class="img-fluid" style="width: 2rem; height: 2.4rem;">-->
    <img src="https://cdn.dribbble.com/users/1523313/screenshots/16134521/media/3975730626bdae63cf9b25d3b634bac3.gif" alt="chatWithDoc" class="img-fluid" style="width: 5.2rem; height: 5rem; border-radius: 100px;">
</button>

<div class="modal border-0" tabindex="-1" id="chatModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 0 0 0 11px;">
                <div class="row w-100">
                    <div class="col-2 bg-theme d-flex justify-content-center align-items-center text-white" style="border-radius: 0.5rem 0 0;">
                        <img src="https://cdn.dribbble.com/users/1523313/screenshots/16134521/media/3975730626bdae63cf9b25d3b634bac3.gif" alt="chatWithDoc" class="img-fluid" style="width: 3rem; height: 3rem; border-radius: 100px;">

                    </div>
                    <div class="col-9">
                        <h5 class="modal-title fs-6" style="padding: 0.5rem;">
                            Supercharge PDF Conversations: Seamlessly Engage with Your PDFs!</h5>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-right: 0.8rem;"></button>
            </div>
            <div class="modal-body">
                <div class="chat-content" id="chatContent" style="height: 15rem; overflow-y: scroll;">
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <textarea type="text" name="chatSendMessage" id="" placeholder="Enter your message here" class="form-control shadow-md border-0"></textarea>
                </div>
                <div class="row w-100">
                    <div class="col-6">
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Close</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn bg-dark text-white w-100" id="sendChatBtn">
                            <div class="spinner-border spinner-border-sm text-light" role="status" style="display: none">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- CHAT PDF MODAL END --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.8/axios.min.js" integrity="sha512-PJa3oQSLWRB7wHZ7GQ/g+qyv6r4mbuhmiDb8BjSFZ8NZ2a42oTtAq5n0ucWAwcQDlikAtkub+tPVCw4np27WCg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        const config = {
            headers: {
                "x-api-key": "sec_qLUcsYBeIWAt564Tk5zhHg76DQHjastL",
                "Content-Type": "application/json",
            },
        };

        function scrollToBottom() {
            var chatContent = $('#chatContent');
            chatContent.animate({
                scrollTop: chatContent.prop('scrollHeight')
            }, 500);
        }

        const data = {
            url: "{{ asset('user/pdf/doc/SOP'. $document->id .'.pdf') }}"
        }

        let srcId = "";

        function getUserMessageHtml(message) {
            let html = `<div class="chat-content-user-chat bg-dark text-white p-3 rounded-3 w-50 mb-2" style="margin-left: auto;">${message}</div>`;
            return html;
        }

        function getResponseMessageHtml(message) {
            let html = `<div class="chat-content-ai-chat bg-light p-3 rounded-3 w-50 mb-2" style="margin-right: auto;">${message}</div>`;
            return html;
        }

        async function initializeChatModal() {
            console.log('initializeChatModal')
            try {
                const addPdfUrl = "https://api.chatpdf.com/v1/sources/add-url";

                const res = await axios.post(addPdfUrl, data, config)

                console.log('res', res);

                srcId = res.data.sourceId;

            } catch (err) {
                console.log('Error in initializeChatModal fn', err.message)
            }
        }

        async function sendChat() {

            let message = $('textarea[name=chatSendMessage]').val();

            if (message && message.trim() !== '') {
                scrollToBottom()

                $('#sendChatBtn').prop('disabled', true);
                $('#sendChatBtn > i').hide();
                $('#sendChatBtn > div').show();

                const chatData = {
                    "sourceId": srcId,
                    "messages": [{
                        "role": "user",
                        "content": message
                    }]
                }

                const userMsgHtml = getUserMessageHtml(message);
                $('#chatContent').append(userMsgHtml).show('slow');
                $('textarea[name=chatSendMessage]').val('');

                try {

                    const chatPdfEndpoint = "https://api.chatpdf.com/v1/chats/message";

                    const res = await axios.post(chatPdfEndpoint, chatData, config)

                    console.log('res', res);

                    let resMsg = res.data.content;

                    const aiResHtml = getResponseMessageHtml(resMsg);
                    $('#chatContent').append(aiResHtml).show('slow');

                } catch (err) {
                    console.log('Error in sendChat fn', err.message)
                }
                $('#sendChatBtn').prop('disabled', false);
                $('#sendChatBtn > i').show();
                $('#sendChatBtn > div').hide();

                scrollToBottom()
            }


        }

        initializeChatModal();

        $('#sendChatBtn').click(function() {
            sendChat();
        })
    })
</script>




<style>
    .group-input input {
        width: 60%;
    }
</style>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var pdfObject = document.querySelector('iframe#theFrame"]');
        var pdfDocument = pdfObject.contentDocument;
        var firstPage = pdfDocument.querySelector('.page:first-of-type');
        firstPage.style.display = 'none';
    });
</script>
<script>
    // JavaScript to open modal when obsolete button is clicked
    document.getElementById('obsolete-button').addEventListener('click', function() {
        $('#signature-modal').modal('show');
    });
</script>
@endsection