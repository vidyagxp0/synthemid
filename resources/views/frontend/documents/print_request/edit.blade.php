@extends('frontend.layout.main')
@section('container')
    <style>
        #fr-logo {
            display: none;
        }

        .fr-logo {
            display: none;
        }

        textarea.note-codable {
            display: none !important;
        }

        header {
            display: none;
        }

        .group-input table input,
        .group-input table select {
            border: 0;
            margin: 0 !important;
            padding: 0 !important;
        }

        .sop-type-header {
            display: grid;
            grid-template-columns: 135px 1fr;
            border: 2px solid #000000;
            margin-bottom: 20px;
        }

        .main-head {
            display: grid;
            place-items: center;
            align-content: center;
            font-size: 1.2rem;
            font-weight: 700;
            border-left: 2px solid #000000;
        }

        .sub-head-2 {
            text-align: center;
            background: #4274da;
            margin-bottom: 20px;
            padding: 10px 20px;
            font-size: 1.5rem;
            color: #fff;
            border: 2px solid #000000;
            border-radius: 40px;
        }

        #displayField {
            border: 1px solid #f0f0f0;
            background: white;
            padding: 20px;
            position: relative;
            display: flex;
            align-items: center;
        }

        #displayField li {
            margin-left: 1rem;
            background-color: #f0f0f0;
            padding: 5px;
        }

        .close-icon {
            color: red;
            margin-left: auto;
            /* Pushes the icon to the right */
            cursor: pointer;
        }

        .progress-bars div {
            flex: 1 1 auto;
            border: 1px solid grey;
            padding: 5px;
            /* border-radius: 20px; */
            text-align: center;
            position: relative;
            /* border-right: none; */
            background: white;
        }

        .state-block {
            padding: 20px;
            margin-bottom: 20px;
        }

        .progress-bars div.active {
            background: green;
            font-weight: bold;
        }

        #change-control-fields>div>div.inner-block.state-block>div.status>div.progress-bars.d-flex>div:nth-child(1) {
            border-radius: 20px 0px 0px 20px;
        }

        #change-control-fields>div>div.inner-block.state-block>div.status>div.progress-bars.d-flex>div:nth-child(6) {
            border-radius: 0px 20px 20px 0px;

        }
    </style>
    <?php $division_id = isset($_GET['id']) ? $_GET['id'] : ''; ?>
    <div id="data-field-head">
        <div class="pr-id">
            Print Request
        </div>
        @if (isset($_GET['id']))
            <div class="division-bar">
                <strong>Site Division/Project</strong> :
                {{ $print_history->divison_id }} / Print Request
            </div>
        @endif
    </div>

    <div id="data-fields">

        <div class="container-fluid">
            <div class="inner-block state-block">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="main-head">Record Workflow </div>

                    <div class="d-flex" style="gap:20px;">
                        @php
                            $userRoles = DB::table('user_roles')
                                ->where(['user_id' => auth()->id(), 'q_m_s_divisions_id' => $print_history->division_id])
                                ->get();
                            $userRoleIds = $userRoles->pluck('q_m_s_roles_id')->toArray();
                        @endphp

                        @if ($print_history->stage == 1 && (in_array(3, $userRoleIds) || in_array(18, $userRoleIds)))
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#signature-modal">
                                Send for HOD Approval
                            </button>
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#cancel-modal">
                                Cancel
                            </button>
                        @elseif($print_history->stage == 2 && (in_array(4, $userRoleIds) || in_array(18, $userRoleIds)))
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#more-info-required-modal">
                                Request More Info.
                            </button>
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#signature-modal">
                                HOD Approval Completed
                            </button>
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#cancel-modal">
                                Reject
                            </button>
                        @elseif($print_history->stage == 3 && (in_array(7, $userRoleIds) || in_array(18, $userRoleIds)))
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#more-info-required-modal">
                                Request More Info.
                            </button>
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#signature-modal">
                                Approved by QA
                            </button>
                            <button class="button_theme1" data-bs-toggle="modal" data-bs-target="#cancel-modal">
                                Reject
                            </button>
                        @endif
                        <button class="button_theme1"> <a class="text-white" href="{{ url('documents') }}"> Exit
                            </a> </button>
                    </div>

                </div>


                <div class="status">
                    <div class="head">Current Status</div>
                    @if ($print_history->stage == 0)
                        <div class="progress-bars ">
                            <div class="bg-danger" style="border-radius: 20px;">Rejected</div>
                        </div>
                    @else
                        <div class="progress-bars d-flex" style="font-size: 15px;">
                            @if ($print_history->stage >= 1)
                                <div class="active">Initiation</div>
                            @else
                                <div class="">Initiation</div>
                            @endif
                            @if ($print_history->stage >= 2)
                                <div class="active">Under HOD Approval</div>
                            @else
                                <div class="">Under HOD Approval</div>
                            @endif
                            @if ($print_history->stage >= 3)
                                <div class="active">Under QA Approval</div>
                            @else
                                <div class="">Under QA Approval</div>
                            @endif
                            @if ($print_history->stage >= 4)
                                <div class="active">Approved</div>
                            @else
                                <div class="">Approved</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="tab">
                <button class="tablinks active" onclick="openData(event, 'doc-info')" id="defaultOpen">General
                    information</button>
                <button class="tablinks" onclick="openData(event, 'hodcft')">HOD Input</button>
                <button class="tablinks" onclick="openData(event, 'qa')">QA Input</button>
                {{-- <button class="tablinks" onclick="openData(event, 'sign')">Signature</button> --}}
            </div>


            <form id="document-form" action="{{ route('print-request.update', $print_history->id) }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div id="step-form">
                    <!-- Tab content -->
                    <div id="doc-info" class="tabcontent">

                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="originator">Requested By</label>
                                        <div class="default-name">{{ Helpers::getInitiatorName($print_history->originator_id) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="open-date">Date Opened</label>
                                        <div class="default-name"> {{ $print_history->created_at }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-type">Sile/Location <span class="text-danger">*</span></label>
                                        <select name="division_id" required>
                                            <option value="1" {{ $print_history->division_id == 1 ? 'selected' : '' }}>Corporate</option>
                                            <option value="2" {{ $print_history->division_id == 2 ? 'selected' : '' }}>Plant</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-type">Request For <span class="text-danger">*</span></label>
                                        <select name="request_for" required>
                                            <option value="Print" {{ $print_history->request_for == "Print" ? 'selected' : '' }}>Print</option>
                                            <option value="Download" {{ $print_history->request_for == "Download" ? 'selected' : '' }}>Download</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="short-desc">Short Description<span class="text-danger">*</span></label>
                                        <span id="new-rchars">255</span>
                                        characters remaining
                                        <input type="text" id="short_desc" name="short_description" value="{{$print_history->short_description}}" maxlength="255">
                                    </div>
                                    <p id="short_descError" style="color:red">**Short description is required</p>

                                </div>

                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-type">Reference Records<span class="text-danger">*</span></label>
                                            <select id="choices-multiple-remove-button" class="choices-multiple-approver" name="reference_records[]" placeholder="Select Reference Records" multiple>
                                                @foreach ($documentList as $doc)
                                                    <option value="{{ $doc->id }}" 
                                                        @if ($print_history->reference_records) 
                                                            @php $data = explode(",",$print_history->reference_records);
                                                                $count = count($data);
                                                                $i=0;
                                                            @endphp
                                                            @for ($i = 0; $i < $count; $i++)
                                                                @if ($data[$i] == $doc->id)
                                                                selected @endif
                                                            @endfor
                                                    >
                                                    {{ Helpers::getDivisionName($doc->division_id) }}/Document/{{ date('Y') }}/{{ Helpers::recordFormat($doc->record)}}/{{ $doc->document_name }}
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            {{-- @foreach ($usersValue as $me)
                                                <option value="{{ $me->id }}" {{ $print_history->permission_user_id == $me->id ? 'selected' : '' }}>{{ $me->name }}</option>
                                            @endforeach --}}
                                    </div>
                                </div>

                                <div class="col-md-4 new-date-data-field">
                                    <div class="group-input input-date">
                                        <label for="due-date">Due Date <span class="text-danger">*</span></label>
                                        <div><small class="text-primary">Kindly Fill Target Date of Completion</small>
                                        </div>
                                        <div class="calenderauditee">                                     
                                            <input type="text"  id="due_dateDoc" value="{{ $print_history->due_date }}"  placeholder="DD-MMM-YYYY" />
                                            <input type="date" name="due_dateDoc" value="{{ $print_history->due_date ? Carbon\Carbon::parse($print_history->due_date)->format('Y-m-d') : ''  }}" readonly {{Helpers::isRevised($print_history->stage)}}
                                            class="hide-input" style="position: absolute; top: 0; left: 0; opacity: 0;"
                                            min="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                                            oninput="handleDateInput(this, 'due_dateDoc')"/>
                                        </div>
                                    </div>
                                    <p id="due_dateDocError" style="color:red">**Due Date is required</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="short-desc">Reason for Print <span class="text-danger">*</span></label>
                                        <textarea id="print_reason" name="print_reason">{{$print_history->print_reason}}</textarea>
                                    </div>
                                    {{-- <p id="short_descError" style="color:red">**Short description is required</p> --}}
                                </div>
                            </div>
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="hods">HOD's</label>
                                        <select name="hods" id="doc-type" required>
                                            @foreach ($hods as $me)
                                                <option value="{{ $me->id }}" {{ $print_history->hods == $me->id ? 'selected' : '' }}>{{ $me->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="hods">QA's</label>
                                        <select name="permission_user_id" id="doc-type">
                                            @foreach ($qa as $me)
                                                <option value="{{ $me->id }}" {{ $print_history->qa == $me->id ? 'selected' : '' }}>{{ $me->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <div class="orig-head">
                            Initiator Information
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">Initial Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div class="file-attachment-list" id="initial_attachments"></div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input disabled type="file" id="initial_attachments"
                                                    name="initial_attachments[]"
                                                    onclick="addMultipleFiles(this, 'initial_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">Initiated By</label>
                                        <input readonly type="text" name="initiated_by" value="{{Helpers::getInitiatorName($print_history->initiated_by)}}" id="initiated_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="initiated On" style="font-weight: 100">Initiated On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="initiated_on" value="{{Helpers::getdateFormat($print_history->initiated_on)}}" readonly placeholder="DD-MM-YYYY" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="button-block">
                            <button type="submit" value="save" name="submit" id="DocsaveButton"
                                class="saveButton">Save</button>
                            <button type="button" class="nextButton" id="DocnextButton">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white"> Exit
                                </a>
                            </button>
                        </div>
                    </div>
                    <div id="hodcft" class="tabcontent">
                        <div class="orig-head">
                            HOD Input
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="comments">HOD Remarks <span @if (in_array(Auth::user()->id, explode(",", $print_history->hods)) && $print_history->stage == 2)  @else style="display: none" @endif class="text-danger">*</span></label>
                                        <textarea {{Helpers::isRevised($print_history->stage)}} @if (in_array(Auth::user()->id, explode(",", $print_history->hods)) && $print_history->stage == 2) required @else readonly @endif  name="hod_remarks">{{$print_history->hod_remarks}}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="group-input">
                                        <label for="QA Initial Attachments">HOD Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div disabled class="file-attachment-list" id="hod_attachments">
                                                @if ($print_history->hod_attachments)
                                                    @foreach (json_decode($print_history->hod_attachments) as $file)
                                                        <h6 type="button" class="file-container text-dark"
                                                            style="background-color: rgb(243, 242, 240);">
                                                            <b>{{ $file }}</b>
                                                            <a href="{{ asset('upload/' . $file) }}"
                                                                target="_blank"><i class="fa fa-eye text-primary"
                                                                    style="font-size:20px; margin-right:-10px;"></i></a>
                                                            <a type="button" class="remove-file"
                                                            data-remove-id="hod_attachmentsFile-{{ $loop->index }}"
    
                                                                data-file-name="{{ $file }}"><i
                                                                    class="fa-solid fa-circle-xmark"
                                                                    style="color:red; font-size:20px;"></i></a>
                                                        </h6>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input type="file" id="myfile"
                                                    name="hod_attachments[]"{{ $print_history->stage == 0 || $print_history->stage == 13 ? 'disabled' : '' }}
                                                    oninput="addMultipleFiles(this, 'hod_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">HOD Completed By</label>
                                        <input readonly type="text" name="hod_by" id="hod_by"  value="{{Helpers::getInitiatorName($print_history->hod_by)}}">
    
                                    </div>
                                </div>
    
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="HOD Completed On"  style="font-weight: 100;">HOD Completed On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="hod_on" readonly value="{{Helpers::getdateFormat($print_history->hod_on)}}" placeholder="DD-MM-YYYY" />
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" id="DocsaveButton" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" id="DocnextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white" > Exit </a>
                            </button>
                        </div>
                    </div>
    
                    <div id="qa" class="tabcontent">
                        <div class="orig-head">
                            QA Input
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="comments">QA Remarks <span @if (in_array(Auth::user()->id, explode(",", $print_history->qa)) && $print_history->stage == 3)  @else style="display: none" @endif class="text-danger">*</span></label>
                                        <textarea {{Helpers::isRevised($print_history->stage)}} @if (in_array(Auth::user()->id, explode(",", $print_history->qa)) && $print_history->stage == 3) required @else readonly @endif  name="qa_remarks">{{$print_history->qa_remarks}}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="group-input">
                                        <label for="QA Initial Attachments">QA Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div disabled class="file-attachment-list" id="qa_attachments">
                                                @if ($print_history->qa_attachments)
                                                    @foreach (json_decode($print_history->qa_attachments) as $file)
                                                        <h6 type="button" class="file-container text-dark"
                                                            style="background-color: rgb(243, 242, 240);">
                                                            <b>{{ $file }}</b>
                                                            <a href="{{ asset('upload/' . $file) }}"
                                                                target="_blank"><i class="fa fa-eye text-primary"
                                                                    style="font-size:20px; margin-right:-10px;"></i></a>
                                                            <a type="button" class="remove-file"
                                                            data-remove-id="qa_attachmentsFile-{{ $loop->index }}"
    
                                                                data-file-name="{{ $file }}"><i
                                                                    class="fa-solid fa-circle-xmark"
                                                                    style="color:red; font-size:20px;"></i></a>
                                                        </h6>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input type="file" id="myfile"
                                                    name="qa_attachments[]" {{ $print_history->stage == 0 || $print_history->stage == 13 ? 'disabled' : '' }}
                                                    oninput="addMultipleFiles(this, 'qa_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">QA Completed By</label>
                                        <input readonly type="text" name="qa_by" id="qa_by"  value="{{Helpers::getInitiatorName($print_history->qa_by)}}">
    
                                    </div>
                                </div>
    
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="QA Completed On"  style="font-weight: 100;">QA Completed On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="qa_on" readonly value="{{Helpers::getdateFormat($print_history->qa_on)}}" placeholder="DD-MM-YYYY" />
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" id="DocsaveButton" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" id="DocnextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white" > Exit </a>
                            </button>
                        </div>
                    </div>
                    <div id="sign" class="tabcontent">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Initiated By
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Initiated On
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        HOD Approved By
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        HOD Approved On
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        QA Approved By
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        QA Approved On
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="submit">Submit</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white"
                                    href="#"> Exit </a> </button>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="signature-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('print-request.stage', $print_history->id) }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">E-Signature</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
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
                            <input type="comment" name="comment" >
                        </div>

                        @php
                            $hideSubmitButton = false;
                        @endphp

                        @if ($print_history->stage == 2 && empty($print_history->hod_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the HOD input are completed before proceeding with the activity to Under QA Approval.</div>
                            @php $hideSubmitButton = true; @endphp
                        @elseif ($print_history->stage == 3 && empty($print_history->qa_remarks))
                            <div style="color: red">Note: Please ensure that all required fields in the QA input are completed before proceeding with the activity to Approved.</div>
                            @php $hideSubmitButton = true; @endphp
                        @endif

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        @if (!$hideSubmitButton)
                            <button type="submit">Submit</button>
                        @endif
                        <button type="button" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="more-info-required-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('print-request.stagereject',  $print_history->id) }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">E-Signature</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
                        </div>
                        <div class="group-input">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input class="input_width" type="text" name="username" required>
                        </div>
                        <div class="group-input">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input class="input_width" type="password" name="password" required>
                        </div>
                        <div class="group-input">
                            <label for="comment">Comment<span class="text-danger">*</span></label>
                            <input class="input_width" type="comment" name="comment" required>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" data-bs-dismiss="modal">Submit</button>
                        <button type="button" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="cancel-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('print-request.cancel', $print_history->id) }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">E-Signature</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="mb-3 text-justify">
                            Please select a meaning and a outcome for this task and enter your username
                            and password for this task. You are performing an electronic signature,
                            which is legally binding equivalent of a hand written signature.
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
                            <label for="comment">Comment<span class="text-danger">*</span></label>
                            <input type="comment" name="comment" required>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" data-bs-dismiss="modal">Submit</button>
                        <button type="button" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
            </form>
        </div>
    </div>

    <style>
        #step-form>div {
            display: none
        }

        #step-form>div:nth-child(1) {
            display: block;
        }
    </style>

    <script src="https://cdn.tiny.cloud/1/5vbh0y1nq5y6uokc071mjvy9n4fnss5ctasrjft7x7ajm9fl/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        $(document).ready(function() {

            const api_key = '{{ env('OPEN_AI_KEY') }}';

            const languages = [
                "Afrikaans", "Albanian", "Amharic", "Arabic", "Armenian", "Azerbaijani",
                "Basque", "Belarusian", "Bengali", "Bosnian", "Bulgarian",
                "Catalan", "Cebuano", "Chichewa", "Chinese (Simplified)", "Chinese (Traditional)",
                "Corsican", "Croatian", "Czech", "Danish", "Dutch", "English", "Esperanto", "Estonian",
                "Filipino", "Finnish", "French", "Frisian", "Galician", "Georgian", "German", "Greek",
                "Gujarati", "Haitian Creole", "Hausa", "Hawaiian", "Hebrew", "Hindi", "Hmong", "Hungarian",
                "Icelandic", "Igbo", "Indonesian", "Irish", "Italian", "Japanese", "Javanese", "Kannada",
                "Kazakh", "Khmer", "Kinyarwanda", "Korean", "Kurdish (Kurmanji)", "Kyrgyz",
                "Lao", "Latin", "Latvian", "Lithuanian", "Luxembourgish", "Macedonian", "Malagasy", "Malay",
                "Malayalam", "Maltese", "Maori", "Marathi", "Mongolian", "Myanmar (Burmese)", "Nepali",
                "Norwegian", "Odia (Oriya)", "Pashto", "Persian", "Polish", "Portuguese", "Punjabi", "Romanian",
                "Russian", "Samoan", "Scots Gaelic", "Serbian", "Sesotho", "Shona", "Sindhi", "Sinhala",
                "Slovak", "Slovenian", "Somali", "Spanish", "Sundanese", "Swahili", "Swedish",
                "Tajik", "Tamil", "Tatar", "Telugu", "Thai", "Turkish", "Turkmen", "Ukrainian", "Urdu",
                "Uyghur", "Uzbek", "Vietnamese", "Welsh", "Xhosa", "Yiddish", "Yoruba", "Zulu"
            ];

            const languageObjects = languages.map(language => ({
                title: language,
                prompt: `Translate this to ${language} language.`,
                selection: true
            }));

            tinymce.init({
                selector: 'textarea.tiny', // Replace this CSS selector to match the placeholder element for TinyMCE
                plugins: 'ai preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen link codesample table charmap pagebreak nonbreaking anchor tableofcontents insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker editimage help formatpainter permanentpen pageembed charmap mentions quickbars linkchecker emoticons advtable footnotes mergetags autocorrect typography advtemplate markdown',
                toolbar: 'undo redo | aidialog aishortcuts | charmap | blocks fontsizeinput | bold italic | align numlist bullist | link | table pageembed | lineheight  outdent indent | strikethrough forecolor backcolor formatpainter removeformat | emoticons checklist | code fullscreen preview | save print | pagebreak anchor codesample footnotes mergetags | addtemplate inserttemplate | addcomment showcomments | ltr rtl casechange | spellcheckdialog a11ycheck',
                ai_request: (request, respondWith) => {
                    const openAiOptions = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${api_key}`
                        },
                        body: JSON.stringify({
                            model: 'gpt-3.5-turbo',
                            temperature: 0.7,
                            max_tokens: 800,
                            messages: [{
                                role: 'user',
                                content: request.prompt
                            }],
                        })
                    };
                    respondWith.string((signal) => window.fetch(
                            'https://api.openai.com/v1/chat/completions', {
                                signal,
                                ...openAiOptions
                            })
                        .then(async (response) => {
                            if (response) {
                                const data = await response.json();
                                if (data.error) {
                                    throw new Error(
                                        `${data.error.type}: ${data.error.message}`);
                                } else if (response.ok) {
                                    // Extract the response content from the data returned by the API
                                    return data?.choices[0]?.message?.content?.trim();
                                }
                            } else {
                                throw new Error('Failed to communicate with the AI');
                            }
                        })
                    );
                },
                ai_shortcuts: [{
                        title: 'Translate',
                        subprompts: languageObjects
                    },
                    {
                        title: 'Summarize content',
                        prompt: 'Provide the key points and concepts in this content in a succinct summary.',
                        selection: true
                    },
                    {
                        title: 'Improve writing',
                        prompt: 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning.',
                        selection: true
                    },
                    {
                        title: 'Simplify language',
                        prompt: 'Rewrite this content with simplified language and reduce the complexity of the writing, so that the content is easier to understand.',
                        selection: true
                    },
                    {
                        title: 'Expand upon',
                        prompt: 'Expand upon this content with descriptive language and more detailed explanations, to make the writing easier to understand and increase the length of the content.',
                        selection: true
                    },
                    {
                        title: 'Trim content',
                        prompt: 'Remove any repetitive, redundant, or non-essential writing in this content without changing the meaning or losing any key information.',
                        selection: true
                    },
                    {
                        title: 'Change tone',
                        subprompts: [{
                                title: 'Professional',
                                prompt: 'Rewrite this content using polished, formal, and respectful language to convey professional expertise and competence.',
                                selection: true
                            },
                            {
                                title: 'Casual',
                                prompt: 'Rewrite this content with casual, informal language to convey a casual conversation with a real person.',
                                selection: true
                            },
                            {
                                title: 'Direct',
                                prompt: 'Rewrite this content with direct language using only the essential information.',
                                selection: true
                            },
                            {
                                title: 'Confident',
                                prompt: 'Rewrite this content using compelling, optimistic language to convey confidence in the writing.',
                                selection: true
                            },
                            {
                                title: 'Friendly',
                                prompt: 'Rewrite this content using friendly, comforting language, to convey understanding and empathy.',
                                selection: true
                            },
                        ]
                    },
                    {
                        title: 'Change style',
                        subprompts: [{
                                title: 'Business',
                                prompt: 'Rewrite this content as a business professional with formal language.',
                                selection: true
                            },
                            {
                                title: 'Legal',
                                prompt: 'Rewrite this content as a legal professional using valid legal terminology.',
                                selection: true
                            },
                            {
                                title: 'Journalism',
                                prompt: 'Rewrite this content as a journalist using engaging language to convey the importance of the information.',
                                selection: true
                            },
                            {
                                title: 'Medical',
                                prompt: 'Rewrite this content as a medical professional using valid medical terminology.',
                                selection: true
                            },
                            {
                                title: 'Poetic',
                                prompt: 'Rewrite this content as a poem using poetic techniques without losing the original meaning.',
                                selection: true
                            },
                        ]
                    }
                ],
                paste_data_images: true,
                images_upload_url: false,
                images_upload_handler: false,
                automatic_uploads: false

            });
        })
    </script>
    <script>
        VirtualSelect.init({
            ele: '#reference_record, #notify_to'
        });

        $('#summernote').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear', 'italic']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('.summernote').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear', 'italic']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        let referenceCount = 1;

        function addReference() {
            referenceCount++;
            let newReference = document.createElement('div');
            newReference.classList.add('row', 'reference-data-' + referenceCount);
            newReference.innerHTML = `
            <div class="col-lg-6">
                <input type="text" name="reference-text">
            </div>
            <div class="col-lg-6">
                <input type="file" name="references" class="myclassname">
            </div><div class="col-lg-6">
                <input type="file" name="references" class="myclassname">
            </div>
        `;
            let referenceContainer = document.querySelector('.reference-data');
            referenceContainer.parentNode.insertBefore(newReference, referenceContainer.nextSibling);
        }
    </script>

    <script>
        var maxLength = 255;
        $('#docname').keyup(function() {
            var textlen = maxLength - $(this).val().length;
            $('#rchars').text(textlen);
        });
    </script>
    <script>
        var maxLength = 255;
        $('#short_desc').keyup(function() {
            var textlen = maxLength - $(this).val().length;
            $('#new-rchars').text(textlen);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#document-form').validate({
                rules: {
                    name: 'required',
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                // Add custom messages if needed
                messages: {
                    name: 'Please enter your name',
                    email: {
                        required: 'Please enter your email',
                        email: 'Please enter a valid email address'
                    },
                    password: {
                        required: 'Please enter a password',
                        minlength: 'Password must be at least 6 characters long'
                    }
                },
                submitHandler: function(form) {
                    form.submit(); // Submit the form if validation passes
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#addButton').click(function() {
                var sourceValue = $('#sourceField').val()
            .trim(); // Get the trimmed value from the source field
                if (!sourceValue) return; // Prevent adding empty values

                // Create a new list item with the source value and a close icon
                var newItem = $('<li>', {
                    class: 'd-flex justify-content-between align-items-center'
                }).text(sourceValue);
                var closeButton = $('<span>', {
                    text: '×',
                    class: 'close-icon ms-2' // Bootstrap class for margin-left spacing
                }).appendTo(newItem);

                // Append the new list item to the display field
                $('#displayField').append(newItem);

                // Create a corresponding option in the hidden select
                var newOption = $('<option>', {
                    value: sourceValue,
                    text: sourceValue,
                    selected: 'selected'
                }).appendTo('#keywords');

                // Clear the input field
                $('#sourceField').val('');

                // Add click event for the close icon
                closeButton.on('click', function() {
                    var thisValue = $(this).parent().text().slice(0, -
                    1); // Remove the '×' from the value
                    $(this).parent().remove(); // Remove the parent list item on click
                    $('#keywords option').filter(function() {
                        return $(this).val() === thisValue;
                    }).remove(); // Also remove the corresponding option from the select
                });
            });
        });

        $(document).on('click', '.removeTag', function() {
            $(this).remove();
        });
    </script>
    <script>
        function openData(evt, cityName) {
            var i, cctabcontent, cctablinks;
            cctabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < cctabcontent.length; i++) {
                cctabcontent[i].style.display = "none";
            }
            cctablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < cctablinks.length; i++) {
                cctablinks[i].className = cctablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";

            // Find the index of the clicked tab button
            const index = Array.from(cctablinks).findIndex(button => button === evt.currentTarget);

            // Update the currentStep to the index of the clicked tab
            currentStep = index;
        }

        const saveButtons = document.querySelectorAll(".saveButton");
        const nextButtons = document.querySelectorAll(".nextButton");
        const form = document.getElementById("step-form");
        const stepButtons = document.querySelectorAll(".tablinks");
        const steps = document.querySelectorAll(".tabcontent");
        let currentStep = 0;

        function nextStep() {
            // Check if there is a next step
            if (currentStep < steps.length - 1) {
                // Hide current step
                steps[currentStep].style.display = "none";

                // Show next step
                steps[currentStep + 1].style.display = "block";

                // Add active class to next button
                stepButtons[currentStep + 1].classList.add("active");

                // Remove active class from current button
                stepButtons[currentStep].classList.remove("active");

                // Update current step
                currentStep++;
            }
        }

        function previousStep() {
            // Check if there is a previous step
            if (currentStep > 0) {
                // Hide current step
                steps[currentStep].style.display = "none";

                // Show previous step
                steps[currentStep - 1].style.display = "block";

                // Add active class to previous button
                stepButtons[currentStep - 1].classList.add("active");

                // Remove active class from current button
                stepButtons[currentStep].classList.remove("active");

                // Update current step
                currentStep--;
            }
        }
    </script>
@endsection
