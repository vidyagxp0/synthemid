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
    margin-left: auto; /* Pushes the icon to the right */
    cursor: pointer;
}


    </style>
<?php $division_id = isset($_GET['id'])?$_GET['id']:'';?>
    <div id="data-field-head">
        <div class="pr-id">
            New Document
        </div>
        @if(isset($_GET['id']))
        <div class="division-bar">
            <strong>Site Division/Project</strong> :
            {{ Helpers::getDivisionName($_GET['id'])}} / Document 
            {{-- {{ $division->dname }} / {{ $division->pname }} --}}
        </div>
        @endif
    </div>

    <div id="data-fields">

        <div class="container-fluid">
            <div class="tab">
                <button class="tablinks active" onclick="openData(event, 'doc-info')" id="defaultOpen">General information</button> 
                <button class="tablinks" onclick="openData(event, 'drafters')">Author Input</button>
                <button class="tablinks" onclick="openData(event, 'hodcft')">HODs Input</button>
                <button class="tablinks" onclick="openData(event, 'qa')">QA Input</button>
                <button class="tablinks" onclick="openData(event, 'reviewers')">Reviewer Input</button>
                <button class="tablinks" onclick="openData(event, 'approvers')">Approver Input</button>
                <button class="tablinks" onclick="openData(event, 'doc-content')">Document Content</button>
                <!-- <button class="tablinks" onclick="openData(event, 'hod-remarks-tab')">HOD Remarks</button> -->
                <button class="tablinks" onclick="openData(event, 'annexures')">Annexures</button>
                <button class="tablinks" onclick="openData(event, 'distribution-retrieval')">Distribution & Retrieval</button>
                <button class="tablinks" onclick="openData(event, 'sign')">Signature</button>
                <button class="tablinks printdoc" style="float: right;" onclick="window.print();return false;" >Print</button>
            </div>

            <form id="document-form" action="{{ route('documents.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div id="step-form">
                    <!-- Tab content -->
                    <div id="doc-info" class="tabcontent">

                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="originator">Originator</label>
                                        <div class="default-name">{{ Auth::user()->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="open-date">Date Opened</label>
                                        <div class="default-name"> {{ date('d-M-Y') }}</div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="record-num">Record Number</label>
                                        <div class="default-name">{{ $recordNumber }}</div>
                                    </div>
                                </div> --}}
                                        
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        @if(isset($_GET['id']))
                                        <label for="Division Code"><b>Site/Location Code</b></label>
                                        <input readonly type="text" name="division_id" value="{{ Helpers::getDivisionName($_GET['id'])}}">
                                        <input type="hidden" name="division_id" value="{{$_GET['id']}}">
                                        @else
                                        <label for="Division Code"><b>Site/Location Code </b></label>
                                        <input readonly type="text" name="division_code"
                                        value="{{ Helpers::getDivisionName(session()->get('division')) }}">
                                       <input type="hidden" name="division_id" value="{{ session()->get('division') }}">
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="document_name-desc">Document Name<span
                                                class="text-danger">*</span></label><span id="rchars">255</span>
                                        characters remaining
                                        <input id="docname" type="text" name="document_name" maxlength="255" required>
                                    </div>
                                    <p id="docnameError" style="color:red">**Document Name is required</p>

                                </div>

                                
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="short-desc">Short Description<span class="text-danger">*</span></label>
                                        <span id="new-rchars">255</span>
                                        characters remaining
                                        <input type="text" id="short_desc" name="short_desc" maxlength="255">
                                    </div>
                                    <p id="short_descError" style="color:red">**Short description is required</p>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                            <label for="cc_reference_record">Change Control Reference Records</label>
                                            <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                                name="cc_reference_record[]" placeholder="Select Reference Records" multiple>
                                                @foreach ($ccrecord as $document)
                                                    <option value="{{ $document->id }}">
                                                        {{ Helpers::getDivisionName($document->division_id)}}/CC/{{date('Y')}}/{{Helpers::recordFormat($document->record)}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                                <div class="col-md-6">
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-type">Document Type<span class="text-danger">*</span></label>
                                        <select name="document_type_id" id="doc-type" required>
                                            <option value="" selected>Enter your Selection</option>
                                            @foreach (Helpers::getDocumentTypes() as $code => $type)
                                                <option data-id="{{ $code }}" value="{{ $code }}">
                                                    {{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p id="doc-typeError" style="color:red">** Document is required</p>

                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-code">Document Type Code</label>
                                        <div class="default-name"> <span id="document_type_code">Not selected</span></div>               
                                     </div>
                                </div>

                                <div class="col-md-4 new-date-data-field">
                                    <div class="group-input input-date">
                                        <label for="due-date">Due Date <span class="text-danger">*</span></label>
                                        <div><small class="text-primary">Kindly Fill Target Date of Completion</small>
                                        </div>
                                        <div class="calenderauditee"> 
                                            <input type="text" name="due_dateDoc" id="due_dateDoc"  readonly placeholder="DD-MMM-YYYY" />                                    
                                        <input
                                         type="date" id="due_dateDoc" name="due_dateDoc" pattern="\d{4}-\d{2}-\d{2}"
                                         class="hide-input" min="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                                         oninput="handleDateInput(this, 'due_dateDoc')"/>
                                        </div>
                                    </div>
                                    <p id="due_dateDocError" style="color:red">**Due Date is required</p>

                                </div>
                            </div>
                        </div>                                
                        <div class="orig-head">
                            Other Information
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="drafter">Author<span class="text-danger">*</span></label>
                                        <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                            name="drafters[]" placeholder="Select Author" multiple required>
                                            @if (!empty($drafter))
                                                @foreach ($drafter as $lan)
                                                    @if(Helpers::checkUserRolesDrafter($lan))
                                                        <option value="{{ $lan->id }}">
                                                            {{ $lan->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="hods">HODs<span class="text-danger">*</span></label>
                                        <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                            name="hods[]" placeholder="Select HODs" multiple required>
                                            @foreach ($hods as $hod)
                                                <option value="{{ $hod->id }}">
                                                    {{ $hod->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="hods">QAs<span class="text-danger">*</span></label>
                                        <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                            name="qa[]" placeholder="Select QAs" multiple required>
                                            @foreach ($qa as $hod)
                                                <option value="{{ $hod->id }}">
                                                    {{ $hod->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="reviewers">Reviewers<span class="text-danger">*</span></label>
                                        <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                            name="reviewers[]" placeholder="Select Reviewers" multiple required>
                                            @if (!empty($reviewer))
                                                @foreach ($reviewer as $lan)
                                                    @if(Helpers::checkUserRolesreviewer($lan))
                                                        <option value="{{ $lan->id }}">
                                                            {{ $lan->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>

                                    </div>
                                    {{-- <p id="reviewerError" style="color:red">** Reviewers are required</p> --}}
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="approvers">Approvers<span class="text-danger">*</span></label>
                                        <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                            name="approvers[]" placeholder="Select Approvers" multiple required>
                                            @if (!empty($approvers))
                                                @foreach ($approvers as $lan)
                                                    @if(Helpers::checkUserRolesApprovers($lan))
                                                        <option value="{{ $lan->id }}">
                                                            {{ $lan->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <p id="approverError" style="color:red">** Approvers are required</p>
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
                                                <input disabled type="file" id="initial_attachments" name="initial_attachments[]"
                                                    onclick="addMultipleFiles(this, 'initial_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">Initiated By</label>
                                        <input readonly type="text" name="initiated_by" id="initiated_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="initiated On" style="font-weight: 100">Initiated On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="initiated_on" readonly placeholder="DD-MM-YYYY" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="button-block">
                            <button type="submit" value="save" name="submit" id="DocsaveButton" class="saveButton">Save</button>
                            <button type="button" class="nextButton" id="DocnextButton">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white"> Exit </a>
                            </button> 
                        </div>
                    </div>
                    <div id="drafters" class="tabcontent">
                        <div class="orig-head">
                            Author Input
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="comments">Drafter Remarks</label>
                                        <textarea disabled name="drafter_remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">Drafter Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div class="file-attachment-list" id="drafter_attachments"></div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input disabled type="file" id="drafter_attachments" name="drafter_attachments[]"
                                                    onclick="addMultipleFiles(this, 'drafter_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">Drafted By</label>
                                        <input readonly type="text" name="drafted_by" id="drafted_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="Drafted On">Drafted On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="drafted_on" readonly placeholder="DD-MM-YYYY" />
                                            <input type="date" name="drafted_on"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="hide-input"
                                                oninput="handleDateInput(this, 'drafted_on')" />
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
                    <div id="hodcft" class="tabcontent">
                        <div class="orig-head">
                            HODs Input
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="comments">HODs Remarks</label>
                                        <textarea disabled name="hod_remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">HODs Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div class="file-attachment-list" id="hod_attachments"></div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input disabled type="file" id="hod_attachments" name="hod_attachments[]"
                                                    onclick="addMultipleFiles(this, 'hod_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">HODs Completed By</label>
                                        <input readonly type="text" name="hod_by" id="hod_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="HODs Completed On">HODs Completed On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="hod_on" readonly placeholder="DD-MM-YYYY" />
                                            <input type="date" name="hod_on"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="hide-input"
                                                oninput="handleDateInput(this, 'hod_on')" />
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
                                        <label for="comments">QA Remarks</label>
                                        <textarea disabled name="qa_remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">QA Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div class="file-attachment-list" id="qa_attachments"></div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input disabled type="file" id="qa_attachments" name="qa_attachments[]"
                                                    onclick="addMultipleFiles(this, 'qa_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">QA Completed By</label>
                                        <input readonly type="text" name="qa_by" id="qa_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="QA Completed On">QA Completed On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="qa_on" readonly placeholder="DD-MM-YYYY" />
                                            <input type="date" name="qa_on"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="hide-input"
                                                oninput="handleDateInput(this, 'qa_on')" />
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
                    <div id="reviewers" class="tabcontent">
                        <div class="orig-head">
                            Reviewer Input
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="comments">Reviewer Remarks</label>
                                        <textarea disabled name="reviewer_remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">Reviewer Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div class="file-attachment-list" id="reviewer_attachments"></div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input disabled type="file" id="reviewer_attachments" name="reviewer_attachments[]"
                                                    onclick="addMultipleFiles(this, 'reviewer_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">Reviewer Completed By</label>
                                        <input readonly type="text" name="reviewer_by" id="reviewer_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="QA Completed On">Reviewer Completed On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="reviewer_on" readonly placeholder="DD-MM-YYYY" />
                                            <input type="date" name="reviewer_on"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="hide-input"
                                                oninput="handleDateInput(this, 'reviewer_on')" />
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
                    <div id="approvers" class="tabcontent">
                        <div class="orig-head">
                            Approver Input
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="comments">Approver Remarks</label>
                                        <textarea disabled name="approver_remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">Approver Attachments</label>
                                        <div><small class="text-primary">Please Attach all relevant or supporting
                                                documents</small></div>
                                        <div class="file-attachment-field">
                                            <div class="file-attachment-list" id="approver_attachments"></div>
                                            <div class="add-btn">
                                                <div>Add</div>
                                                <input disabled type="file" id="approver_attachments" name="approver_attachments[]"
                                                    onclick="addMultipleFiles(this, 'approver_attachments')" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 warehouse">
                                    <div class="group-input">
                                        <label for="Warehousefeedback">Approver Completed By</label>
                                        <input readonly type="text" name="approver_by" id="approver_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="QA Completed On">Approver Completed On</label>
                                        <div class="calenderauditee">
                                            <input type="text" id="approver_on" readonly placeholder="DD-MM-YYYY" />
                                            <input type="date" name="approver_on"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="hide-input"
                                                oninput="handleDateInput(this, 'approver_on')" />
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
            
                   
                    <div id="doc-content" class="tabcontent">
                        <div class="orig-head">
                            Document Information
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="purpose">Objective</label>
                                        <textarea name="purpose"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="scope">Scope</label>
                                        <textarea name="scope"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="group-input">
                                        
                                        <label for="responsibility" id="responsibility">
                                            Responsibility<button type="button" id="responsibilitybtnadd"
                                                name="button">+</button>
                                                <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        </label>
                                        
                                        <div id="responsibilitydiv">
                                            <div class="singleResponsibilityBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="responsibility[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subResponsibilityAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="group-input">
                                        
                                        <label for="accountability" id="accountability">
                                            Accountability<button type="button" id="accountabilitybtnadd"
                                                name="button">+</button>
                                                <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        </label>
                                        
                                        <div id="accountabilitydiv">
                                            <div class="singleAccountabilityBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="accountability[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subAccountabilityAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="references" id="references">
                                            References<button type="button" id="referencesbtadd" >+</button>
                                        </label>
                                        <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        <div id="referencesdiv">
                                            <div class="singleReferencesBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="references[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subReferencesAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="abbreviation" id="abbreviation">
                                            Abbreviation<button type="button" id="abbreviationbtnadd"
                                                name="button">+</button>
                                                <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        </label>

                                        
                                        <div id="abbreviationdiv">
                                            <div class="singleAbbreviationBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="abbreviation[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subAbbreviationAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="abbreviation" id="definition">
                                            Definition<button type="button" id="Definitionbtnadd"
                                                name="button">+</button>
                                                <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        </label>

                                        

                                        <div id="definitiondiv">

                                            <div class="singleDefinitionBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="defination[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subDefinitionAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="reporting" id="newreport">
                                            General Instructions<button type="button" id="materialsbtadd"
                                                name="button">+</button>
                                                <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        </label>

                                        <div class="materialsBlock">
                                            <div class="singleMaterialBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="materials_and_equipments[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button type="button" class="subMaterialsAdd" name="button">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="group-input">
                                        <label for="procedure">Procedure</label>
                                        <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        <textarea name="procedure" class="tiny">
                                    </textarea>
                                    </div>
                                </div>

                                

                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="reporting" id="newreport">
                                            Cross References<button type="button" id="reportingbtadd" name="button">+</button> 
                                        </label>
                                        <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        
                                        <div id="reportingdiv">
                                            <div class="singleReportingBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="reporting[]" class=""></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subReportingAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                

                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="ann" id="ann">
                                            Annexure<button type="button" id="annbtadd" >+</button>
                                        </label>
                                        <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                        
                                        <div id="anndiv">
                                            <div class="singleAnnexureBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="ann[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subAnnexureAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row reference-data">
                                            <div class="col-lg-6">
                                                <input type="text" name="reference-text">
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="file" name="references" class="myclassname">
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                                {{-- <div class="col-md-12">  ---By Aditya
                                    <div class="group-input">
                                        <label for="annexure">
                                            Annexure<button type="button" name="ann" id="annexurebtnadd">+</button>
                                        </label>
                                        <table class="table-bordered table" id="annexure">
                                            <div><small class="text-primary">Please mention brief summary</small></div>
                                            <thead>
                                                <tr>
                                                    <th class="sr-num">Sr. No.</th>
                                                    <th class="annx-num">Annexure No.</th>
                                                    <th class="annx-title">Title of Annexure</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input disabled type="text" name="serial_number[]" value="1"></td>
                                                    <td><input type="text" name="annexure_number[]"></td>
                                                    <td><input type="text" name="annexure_data[]"></td>
                                                </tr>
                                                <div id="annexurediv"></div>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> --}}
                                {{-- <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="test">
                                            Revision History<button type="button" name="reporting2"
                                                onclick="addRevRow('revision')">+</button>
                                        </label>
                                        <div><small class="text-primary">Please mention brief summary</small></div>
                                        <table class="table-bordered table" id="revision">
                                            <thead>
                                                <tr>
                                                    <th class="sop-num">SOP Revision No.</th>
                                                    <th class="dcrf-num">Change Control No./ DCRF No.</th>
                                                    <th class="changes">Changes</th>
                                                    <th class="deleteRow">&nbsp;</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" id="rev-num0"></td>
                                                    <td><input type="text" id="control0"></td>
                                                    <td><input type="text" id="change0"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" id="DocsaveButton" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white"> Exit </a>
                            </button>
                        </div>
                    </div>

                    {{-- HOD REMARKS TAB START --}}
                    <div id="hod-remarks-tab" class="tabcontent">

                        <div class="input-fields">
                            <div class="group-input">
                                <label for="hod-remark">HOD Comments</label>
                                <textarea class="summernote" name="hod_comments"></textarea>
                            </div>
                        </div>

                        <div class="input-fields">
                            <div class="group-input">
                                <label for="hod-attachments">HOD Attachments</label>
                                <input type="file" name="hod_attachments[]" multiple>
                            </div>
                        </div>

                        <div class="button-block">
                            <button type="submit" value="save" name="submit" id="DocsaveButton" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white"> Exit </a>
                            </button>
                        </div>

                    </div>
                    {{-- HOD REMARKS TAB END --}}

                    <div id="annexures" class="tabcontent">
                        <div class="input-fields">
                            @for ($i = 1; $i <= 20; $i++)
                                <div class="group-input">
                                    <label for="annexure-{{ $i }}">Annexure A-{{ $i }}</label>
                                    <textarea class="summernote" name="annexuredata[]" id="annexure-{{ $i }}"></textarea>
                                </div>
                            @endfor
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white" > Exit </a>
                            </button>
                        </div>
                    </div>

                    <div id="distribution-retrieval" class="tabcontent">
                        <div class="orig-head">
                            Distribution & Retrieval
                        </div>
                        {{-- <div class="col-md-12 input-fields">
                            <div class="group-input">
                                <label for="distribution" id="distribution">
                                    Distribution & Retrieval<button type="button" id="distributionbtnadd" >+</button>
                                </label>
                                <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                <input type="text" name="distribution[]" class="myclassname">
                                <div id="distributiondiv"></div>
                            </div>
                        </div> --}}
                        <div class="input-fields">
                            <div class="group-input">
                                <label for="distriution_retrieval">
                                    Distribution & Retrieval
                                    <button type="button" name="    "
                                        onclick="addDistributionRetrieval('distribution-retrieval-grid')">+</button>
                                </label>
                                <div class="table-responsive retrieve-table">
                                    <table class="table table-bordered" id="distribution-retrieval-grid">
                                        <thead>
                                            <tr>
                                                <th>Row </th>
                                                <th>Document Title</th>
                                                <th>Document Number</th>
                                                <th>Document Printed By</th>
                                                <th>Document Printed on</th>
                                                <th>Number of Print Copies</th>
                                                <th>Issuance Date</th>
                                                <th>Issued To </th>
                                                <th>Department/Location</th>
                                                <th>Number of Issued Copies</th>
                                                <th>Reason for Issuance</th>
                                                <th>Retrieval Date</th>
                                                <th>Retrieved By</th>
                                                <th>Retrieved Person Department</th>
                                                <th>Number of Retrieved Copies</th>
                                                <th>Reason for Retrieval</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- <tr>
                                                 <td><input type="text" Value="1" name="distribution[0][serial_number]" readonly>
                                                 </td>
                                                 <td><input type="text" name="distribution[0][document_title]">
                                                 </td>
                                                 <td><input type="number" name="distribution[0][document_number]">
                                                 </td>
                                                 <td><input type="text" name="distribution[0][document_printed_by]">
                                                 </td>
                                                 <td><input type="text" name="distribution[0][document_printed_on]">
                                                 </td>
                                                 <td><input type="number" name="distribution[0][document_printed_copies]">
                                                 </td>
                                                 <td><div class="group-input new-date-data-field mb-0">
                                                    <div class="input-date "><div
                                                     class="calenderauditee">
                                                    <input type="text" id="issuance_date' + serialNumber +'" readonly placeholder="DD-MMM-YYYY" />
                                                    <input type="date" name="distribution[0][issuance_date]" class="hide-input" 
                                                    oninput="handleDateInput(this, `issuance_date' + serialNumber +'`)" /></div></div></div>
                                                </td>
                                                
                                                    <td>
                                                        <select id="select-state" placeholder="Select..."
                                                            name="distribution[0][issuance_to]">
                                                            <option value='0'>-- Select --</option>
                                                            <option value='1'>Amit Guru</option>
                                                            <option value='2'>Shaleen Mishra</option>
                                                            <option value='3'>Madhulika Mishra</option>
                                                            <option value='4'>Amit Patel</option>
                                                            <option value='5'>Harsh Mishra</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="select-state" placeholder="Select..."
                                                            name="distribution[0][location]">
                                                            <option value='0'>-- Select --</option>
                                                            <option value='1'>Tech Team</option>
                                                            <option value='2'>Quality Assurance</option>
                                                            <option value='3'>Quality Management</option>
                                                            <option value='4'>IT Administration</option>
                                                            <option value='5'>Business Administration</option>
                                                        </select>
                                                    </td>    
                                                <td><input type="number" name="distribution[0][issued_copies]">
                                                </td>
                                                <td><input type="text" name="distribution[0][issued_reason]">
                                                </td>
                                                <td><div class="group-input new-date-data-field mb-0">
                                                    <div class="input-date "><div
                                                     class="calenderauditee">
                                                    <input type="text" id="retrieval_date' + serialNumber +'" readonly placeholder="DD-MMM-YYYY" />
                                                    <input type="date" name="distribution[0][retrieval_date]" class="hide-input" 
                                                    oninput="handleDateInput(this, `retrieval_date' + serialNumber +'`)" /></div></div></div>
                                                </td>
                                                <td>
                                                    <select id="select-state" placeholder="Select..."
                                                        name="distribution[0][retrieval_by]">
                                                        <option value="">Select a value</option>
                                                        <option value='1'>Amit Guru</option>
                                                        <option value='2'>Shaleen Mishra</option>
                                                        <option value='3'>Madhulika Mishra</option>
                                                        <option value='4'>Amit Patel</option>
                                                        <option value='5'>Harsh Mishra</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="select-state" placeholder="Select..."
                                                        name="distribution[0][retrieved_department]">
                                                        <option value='0'>-- Select --</option>
                                                        <option value='1'>Tech Team</option>
                                                        <option value='2'>Quality Assurance</option>
                                                        <option value='3'>Quality Management</option>
                                                        <option value='4'>IT Administration</option>
                                                        <option value='5'>Business Administration</option>
                                                    </select>
                                                </td>    
                                                <td><input type="number" name="distribution[0][retrieved_copies]">
                                                </td>
                                                <td><input type="text" name="distribution[0][retrieved_reason]">
                                                </td>
                                                <td><input type="text" name="distribution[0][remark]">
                                                </td>
                                                <td></td>
                                        </tr> --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> 
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a  href="{{ url('rcms/qms-dashboard') }}" class="text-white"> Exit </a>
                            </button>
                        </div>
                    </div>

                    {{-- <div id="print-download" class="tabcontent">
                        <div class="orig-head">
                            Print Permissions
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="person-print">Person Print Permission</label>
                                        <select id="choices-multiple-remove-button" placeholder="Select Persons" multiple>
                                            <option value="HTML">HTML</option>
                                            <option value="Jquery">Jquery</option>
                                            <option value="CSS">CSS</option>
                                            <option value="Bootstrap 3">Bootstrap 3</option>
                                            <option value="Bootstrap 4">Bootstrap 4</option>
                                            <option value="Java">Java</option>
                                            <option value="Javascript">Javascript</option>
                                            <option value="Angular">Angular</option>
                                            <option value="Python">Python</option>
                                            <option value="Hybris">Hybris</option>
                                            <option value="SQL">SQL</option>
                                            <option value="NOSQL">NOSQL</option>
                                            <option value="NodeJS">NodeJS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <table class="table-bordered table">
                                            <thead>
                                                <th class="person">Person</th>
                                                <th class="permission">Daily</th>
                                                <th class="permission">Weekly</th>
                                                <th class="permission">Monthly</th>
                                                <th class="permission">Quarterly</th>
                                                <th class="permission">Annually</th>
                                            </thead>
                                            <tbody>
                                                <td class="person">
                                                    Amit Patel
                                                </td>
                                                <td class="permission">
                                                    6543
                                                </td>
                                                <td class="permission">
                                                    6543
                                                </td>
                                                <td class="permission">
                                                    6543
                                                </td>
                                                <td class="permission">
                                                    432
                                                </td>
                                                <td class="permission">
                                                    123
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="group-print">Group Print Permission</label>
                                        <select id="choices-multiple-remove-button" placeholder="Select Persons" multiple>
                                            <option value="HTML">HTML</option>
                                            <option value="Jquery">Jquery</option>
                                            <option value="CSS">CSS</option>
                                            <option value="Bootstrap 3">Bootstrap 3</option>
                                            <option value="Bootstrap 4">Bootstrap 4</option>
                                            <option value="Java">Java</option>
                                            <option value="Javascript">Javascript</option>
                                            <option value="Angular">Angular</option>
                                            <option value="Python">Python</option>
                                            <option value="Hybris">Hybris</option>
                                            <option value="SQL">SQL</option>
                                            <option value="NOSQL">NOSQL</option>
                                            <option value="NodeJS">NodeJS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <table class="table-bordered table">
                                            <thead>
                                                <th class="person">Group</th>
                                                <th class="permission">Daily</th>
                                                <th class="permission">Weekly</th>
                                                <th class="permission">Monthly</th>
                                                <th class="permission">Quarterly</th>
                                                <th class="permission">Annually</th>
                                            </thead>
                                            <tbody>
                                                <td class="person">
                                                    QA
                                                </td>
                                                <td class="permission">1</td>
                                                <td class="permission">
                                                    54
                                                </td>
                                                <td class="permission">
                                                    654
                                                </td>
                                                <td class="permission">
                                                    765
                                                </td>
                                                <td class="permission">
                                                    654
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="orig-head">
                            Download Permissions
                        </div>
                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="person-print">Person Download Permission</label>
                                        <select id="choices-multiple-remove-button" placeholder="Select Persons" multiple>
                                            <option value="HTML">HTML</option>
                                            <option value="Jquery">Jquery</option>
                                            <option value="CSS">CSS</option>
                                            <option value="Bootstrap 3">Bootstrap 3</option>
                                            <option value="Bootstrap 4">Bootstrap 4</option>
                                            <option value="Java">Java</option>
                                            <option value="Javascript">Javascript</option>
                                            <option value="Angular">Angular</option>
                                            <option value="Python">Python</option>
                                            <option value="Hybris">Hybris</option>
                                            <option value="SQL">SQL</option>
                                            <option value="NOSQL">NOSQL</option>
                                            <option value="NodeJS">NodeJS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <table class="table-bordered table">
                                            <thead>
                                                <th class="person">Person</th>
                                                <th class="permission">Daily</th>
                                                <th class="permission">Weekly</th>
                                                <th class="permission">Monthly</th>
                                                <th class="permission">Quarterly</th>
                                                <th class="permission">Annually</th>
                                            </thead>
                                            <tbody>
                                                <td class="person">
                                                    Amit Patel
                                                </td>
                                                <td class="permission">1</td>
                                                <td class="permission">
                                                    54
                                                </td>
                                                <td class="permission">
                                                    654
                                                </td>
                                                <td class="permission">
                                                    765
                                                </td>
                                                <td class="permission">
                                                    654
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="group-print">Group Download Permission</label>
                                        <select id="choices-multiple-remove-button" placeholder="Select Persons" multiple>
                                            <option value="HTML">HTML</option>
                                            <option value="Jquery">Jquery</option>
                                            <option value="CSS">CSS</option>
                                            <option value="Bootstrap 3">Bootstrap 3</option>
                                            <option value="Bootstrap 4">Bootstrap 4</option>
                                            <option value="Java">Java</option>
                                            <option value="Javascript">Javascript</option>
                                            <option value="Angular">Angular</option>
                                            <option value="Python">Python</option>
                                            <option value="Hybris">Hybris</option>
                                            <option value="SQL">SQL</option>
                                            <option value="NOSQL">NOSQL</option>
                                            <option value="NodeJS">NodeJS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <table class="table-bordered table">
                                            <thead>
                                                <th class="person">Group</th>
                                                <th class="permission">Daily</th>
                                                <th class="permission">Weekly</th>
                                                <th class="permission">Monthly</th>
                                                <th class="permission">Quarterly</th>
                                                <th class="permission">Annually</th>
                                            </thead>
                                            <tbody>
                                                <td class="person">
                                                    QA
                                                </td>
                                                <td class="permission">1</td>
                                                <td class="permission">
                                                    54
                                                </td>
                                                <td class="permission">
                                                    654
                                                </td>
                                                <td class="permission">
                                                    765
                                                </td>
                                                <td class="permission">
                                                    654
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                            <button type="button"> <a  href="{{ url('rcms/qms-dashboard') }}" class="text-white" > Exit </a>
                            </button>
                        </div>
                    </div> --}}

                    <div id="sign" class="tabcontent">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Originated By 
                                        {{-- Review Proposed By --}}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Review Proposed On
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Originated On 
                                        {{-- Document Reuqest Approved By --}}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Document Reuqest Approved On
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Document Writing Completed By
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Document Writing Completed On
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Reviewd By
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Reviewd On
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Approved By
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Approved On
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="button-block">
                            <button type="submit" value="save" name="submit" class="saveButton">Save</button>
                            <button type="button" class="backButton" onclick="previousStep()">Back</button>
                            <button type="submit">Submit</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white" href="#"> Exit </a> </button>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>

    {{-- ======================================
                  DIVISION MODAL

    ======================================= --}}
    <style>
        #step-form>div {
            display: none
        }

        #step-form>div:nth-child(1) {
            display: block;
        }
    </style>

<script src="https://cdn.tiny.cloud/1/5vbh0y1nq5y6uokc071mjvy9n4fnss5ctasrjft7x7ajm9fl/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    $(document).ready(function() {
        
        const api_key = '{{ env("OPEN_AI_KEY") }}';

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

        // console.log(languageObjects);

        // $(document).ready(function(){
        //     var editor = new FroalaEditor('textarea.tiny', {
        //         key: "uXD2lC7C4B4D4D4J4B11dNSWXf1h1MDb1CF1PLPFf1C1EESFKVlA3C11A8D7D2B4B4G2D3J3==",
        //         imageUploadParam: 'image_param',
        //         imageUploadMethod: 'POST',
        //         imageMaxSize: 20 * 1024 * 1024,
        //         imageUploadURL: "{{ route('api.upload.file') }}",
        //         fileUploadParam: 'image_param',
        //         fileUploadURL: "{{ route('api.upload.file') }}",
        //         videoUploadParam: 'image_param',
        //         videoUploadURL: "{{ route('api.upload.file') }}",
        //         videoMaxSize: 500 * 1024 * 1024,
        //         toolbarButtons: {

        //             'moreText': {

        //                 'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting']

        //             },

        //             'moreParagraph': {

        //                 'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote']

        //             },

        //             'moreRich': {

        //                 'buttons': ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR']

        //             },

        //             'moreMisc': {

        //                 'buttons': ['undo', 'redo', 'fullscreen', 'print', 'getPDF', 'spellChecker', 'selectAll', 'html', 'help'],

        //                 'align': 'right',

        //                 'buttonsVisible': 2

        //             }

        //         }

        //     });

        //     var disabledEditors = new FroalaEditor('textarea.tiny-disable', {
        //         key: "uXD2lC7C4B4D4D4J4B11dNSWXf1h1MDb1CF1PLPFf1C1EESFKVlA3C11A8D7D2B4B4G2D3J3==",
        //     }, function() {
        //         disabledEditors.edit.off();
        //     });

        // }) 
        // new FroalaEditor('.selector', {  toolbarButtons: {  'moreText': {    'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting']  },  'moreParagraph': {    'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote']  },  'moreRich': {    'buttons': ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR']  },  'moreMisc': {    'buttons': ['undo', 'redo', 'fullscreen', 'print', 'getPDF', 'spellChecker', 'selectAll', 'html', 'help'],    'align': 'right',    'buttonsVisible': 2  }}});


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
                    messages: [{ role: 'user', content: request.prompt }],
                })
                };
                respondWith.string((signal) => window.fetch('https://api.openai.com/v1/chat/completions', { signal, ...openAiOptions })
                .then(async (response) => {
                    if (response) {
                    const data = await response.json();
                    if (data.error) {
                        throw new Error(`${data.error.type}: ${data.error.message}`);
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
            ai_shortcuts: [
                { title: 'Translate', subprompts: languageObjects },
                { title: 'Summarize content', prompt: 'Provide the key points and concepts in this content in a succinct summary.', selection: true },
                { title: 'Improve writing', prompt: 'Rewrite this content with no spelling mistakes, proper grammar, and with more descriptive language, using best writing practices without losing the original meaning.', selection: true },
                { title: 'Simplify language', prompt: 'Rewrite this content with simplified language and reduce the complexity of the writing, so that the content is easier to understand.', selection: true },
                { title: 'Expand upon', prompt: 'Expand upon this content with descriptive language and more detailed explanations, to make the writing easier to understand and increase the length of the content.', selection: true },
                { title: 'Trim content', prompt: 'Remove any repetitive, redundant, or non-essential writing in this content without changing the meaning or losing any key information.', selection: true },
                { title: 'Change tone', subprompts: [
                    { title: 'Professional', prompt: 'Rewrite this content using polished, formal, and respectful language to convey professional expertise and competence.', selection: true },
                    { title: 'Casual', prompt: 'Rewrite this content with casual, informal language to convey a casual conversation with a real person.', selection: true },
                    { title: 'Direct', prompt: 'Rewrite this content with direct language using only the essential information.', selection: true },
                    { title: 'Confident', prompt: 'Rewrite this content using compelling, optimistic language to convey confidence in the writing.', selection: true },
                    { title: 'Friendly', prompt: 'Rewrite this content using friendly, comforting language, to convey understanding and empathy.', selection: true },
                ] },
                { title: 'Change style', subprompts: [
                    { title: 'Business', prompt: 'Rewrite this content as a business professional with formal language.', selection: true },
                    { title: 'Legal', prompt: 'Rewrite this content as a legal professional using valid legal terminology.', selection: true },
                    { title: 'Journalism', prompt: 'Rewrite this content as a journalist using engaging language to convey the importance of the information.', selection: true },
                    { title: 'Medical', prompt: 'Rewrite this content as a medical professional using valid medical terminology.', selection: true },
                    { title: 'Poetic', prompt: 'Rewrite this content as a poem using poetic techniques without losing the original meaning.', selection: true },
                ] }
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
            ele: '#reference_record, #notify_to, #cc_reference_record'
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
                var sourceValue = $('#sourceField').val().trim(); // Get the trimmed value from the source field
                if (!sourceValue) return; // Prevent adding empty values

                // Create a new list item with the source value and a close icon
                var newItem = $('<li>', { class: 'd-flex justify-content-between align-items-center' }).text(sourceValue);
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
                    var thisValue = $(this).parent().text().slice(0, -1); // Remove the '×' from the value
                    $(this).parent().remove(); // Remove the parent list item on click
                    $('#keywords option').filter(function() {
                        return $(this).val() === thisValue;
                    }).remove(); // Also remove the corresponding option from the select
                });
            });


            // $('#addButton').click(function() {
            //     var sourceValue = $('#sourceField').val(); // Get the value from the source field
            //     var targetField = $(
            //         '.targetField'); // The target field where the data will be added and selected

            //     // Create a new option with the source value
            //     var newOption = $('<option>', {
            //         value: sourceValue,
            //         text: sourceValue,
            //     });

            //     // Append the new option to the target field
            //     targetField.append(newOption);

            //     // Set the new option as selected
            //     newOption.prop('selected', true);
            //     $('#sourceField').val('');
            // });
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
