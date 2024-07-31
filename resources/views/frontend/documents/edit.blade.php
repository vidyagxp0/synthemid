@extends('frontend.layout.main')
@section('container')
    <link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' /><script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>
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
            list-style-type: none;
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
        div.note-modal-footer > input
        {
            background: black;
        }

    </style>

    <div id="data-fields">
        <div class="container-fluid">
            <div class="tab">
                <button class="tablinks active" onclick="openData(event, 'doc-info')" id="defaultOpen">Document information</button>
                <button class="tablinks" onclick="openData(event, 'drafters')">Author Input</button>
                <button class="tablinks" onclick="openData(event, 'hodcft')">HODs Input</button>
                <button class="tablinks" onclick="openData(event, 'qa')">QA Input</button>
                <button class="tablinks" onclick="openData(event, '456')">Reviewer Input</button>
                <button class="tablinks" onclick="openData(event, '123')">Approver Input</button>
                {{-- <button class="tablinks" onclick="openData(event, 'reviewers')">Reviewer Input</button>
                <button class="tablinks" onclick="openData(event, 'approvers')">Approver Input</button> --}}
                <button class="tablinks" onclick="openData(event, 'add-doc')">Training Information</button>
                <button class="tablinks" onclick="openData(event, 'doc-content')">Document Content</button>
                <!-- <button class="tablinks" onclick="openData(event, 'hod-remarks-tab')">HOD Remarks</button> -->
                <button class="tablinks" onclick="openData(event, 'annexures')">Annexures</button>
                <button class="tablinks" onclick="openData(event, 'distribution-retrieval')">Distribution & Retrieval</button>
                {{-- <button class="tablinks" onclick="openData(event, 'print-download')">Print and Download Control </button> --}}
                <button class="tablinks" onclick="openData(event, 'sign')">Signature</button>
                <button class="tablinks printdoc" style="float: right;" onclick="window.print();return false;" >Print</button>

            </div>
            <form method="POST" action="{{ route('documents.update', $document->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div id="doc-info" class="tabcontent">
                    <div class="input-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="originator">Originator</label>
                                    <div class="default-name">{{Helpers::getInitiatorName($document->originator_id)}}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="open-date">Date Opened</label>
                                    <div class="default-name"> {{ $document->date }}</div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="group-input">
                                    <label for="Division Code"><b>Site/Location Code</b></label>
                                    <input disabled type="text" name="division_code"
                                        value="{{ Helpers::getDivisionName($document->division_id) }}">
                                    {{-- <div class="static">{{ Helpers::getDivisionName(session()->get('division')) }}</div> --}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="document_name-desc">Document Name<span
                                        class="text-danger">*</span></label><span id="rchars">255</span>
                                characters remaining
                                    <input type="text" name="document_name" id="docname" maxlength="255"
                                    {{Helpers::isRevised($document->stage)}}  value="{{ $document->document_name }}" required>


                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Document Name' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                        color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach



                                </div>
                                <p id="docnameError" style="color:red">**Document Name is required</p>

                            </div>

                            <script>
                                var maxLength = 255;
                                $('#docname').keyup(function() {
                                    var textlen = maxLength - $(this).val().length;
                                    $('#rchars').text(textlen);
                                });
                            </script>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>
                                        <input class="input-field" type="text" name="document_name_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>

                            @endif
                           
                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="short-desc">Short Description <span
                                        class="text-danger">*</span>    </label>
                                    <span id="editrchars">255</span>
                                characters remaining
                                    <input type="text" name="short_desc" id="short_desc" maxlength="255"
                                     {{Helpers::isRevised($document->stage)}} 
                                        value="{{ $document->short_description }}">
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Short Description' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                <p id="short_descError" style="color:red">**Short description is required</p>

                            </div>
                            <div class="col-md-6">
                                <label for="document_name-desc">Change Control Reference Record</label>
                                <select multiple id="cc_reference_record" name="cc_reference_record[]">
                                                    @foreach ($ccrecord as $new)
                                                        <option value="{{ $new->id }}"{{ in_array($new->id, explode(',', $document->cc_reference_record)) ? 'selected' : '' }}>
                                                            {{ Helpers::getDivisionName($new->division_id) }}/CC/{{ date('Y') }}/{{ Helpers::recordFormat($new->record) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                            </div>


                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="doc-type">Department Type</label>
                                    <select name="document_type_id" id="doc-type" {{Helpers::isRevised($document->stage)}} >
                                        <option value="">Enter your Selection</option>
                                        @foreach (Helpers::getDocumentTypes() as $code => $type)
                                            <option data-id="{{ $code }}" value="{{ $code }}" {{ $code == $document->document_type_id ? 'selected' : '' }}>
                                                {{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Document' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }}
                                                at {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="document_type_id_comment">
                                        </div>
                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif

                            </div>

                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="doc-code">Department Type Code</label>
                                    <div class="default-name"> <span id="document_type_code">
                                            @foreach (Helpers::getDocumentTypes() as $code => $type)
                                                {{ $code == $document->document_type_id ? $code : '' }}
                                            @endforeach

                                        </span> </div>

                                </div>
                            </div>
                            <p id="doc-typeError" style="color:red">**Department Type is required</p>
                            <div class="col-md-4 new-date-data-field">
                                <div class="group-input input-date">
                                    <label for="due-date">Due Date</label>
                                    <div><small class="text-primary" >Kindly Fill Target Date of Completion</small>
                                    </div>
                                    <div class="calenderauditee">                                     
                                        <input type="text"  id="due_dateDoc" value="{{ $document->due_dateDoc }}"  placeholder="DD-MMM-YYYY" />
                                        <input type="date" name="due_dateDoc" value="{{ $document->due_dateDoc ? Carbon\Carbon::parse($document->due_dateDoc)->format('Y-m-d') : ''  }}" readonly {{Helpers::isRevised($document->stage)}}
                                        class="hide-input" style="position: absolute; top: 0; left: 0; opacity: 0;"
                                        min="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                                        oninput="handleDateInput(this, 'due_dateDoc')"/>
                                    </div>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Due Date' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                <p id="due_dateDocError" style="color:red">**Due Date is required</p>

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                                {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="due_date_comment">
                                        </div>

                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif
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
                                    <label for="drafter">Author</label>
                                    <select   @if($document->stage != 1 && !Helpers::userIsQA()) disabled @endif id="choices-multiple-remove-button" class="choices-multiple-approver" {{ !Helpers::userIsQA() ? Helpers::isRevised($document->stage) : ''}} 
                                        name="drafters[]" placeholder="Select Drafter" multiple>
                                        @if (!empty($drafter))
                                            @foreach ($drafter as $lan)
                                            @if(Helpers::checkUserRolesDrafter($lan))
                                                <option value="{{ $lan->id }}"
                                                    @if ($document->drafters) @php
                                                   $data = explode(",",$document->drafters);
                                                    $count = count($data);
                                                    $i=0;
                                                @endphp
                                                @for ($i = 0; $i < $count; $i++)
                                                    @if ($data[$i] == $lan->id)
                                                     selected @endif
                                                    @endfor
                                            @endif>
                                            {{ $lan->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                        @endif
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Drafter' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                <p id="approverError" style="color:red">**Authors are required</p>

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    {{-- Add Comment  --}}
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }}
                                                at {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="drafter_comment">
                                        </div>
                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif

                            </div>

                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="hods">HOD's</label>
                                    <select   @if($document->stage != 1 && !Helpers::userIsQA()) disabled @endif id="choices-multiple-remove-button" class="choices-multiple-approver" {{ !Helpers::userIsQA() ? Helpers::isRevised($document->stage) : ''}} 
                                        name="hods[]" placeholder="Select HOD's" multiple>
                                        @foreach ($hods as $hod)
                                                <option value="{{ $hod->id }}"
                                                    @if ($document->hods) @php
                                                   $data = explode(",",$document->hods);
                                                    $count = count($data);
                                                    $i=0;
                                                @endphp
                                                @for ($i = 0; $i < $count; $i++)
                                                    @if ($data[$i] == $hod->id)
                                                     selected @endif
                                                @endfor>
                                            {{ $hod->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Approvers' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                {{-- <p id="approverError" style="color:red">**Approvers are required</p> --}}

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    {{-- Add Comment  --}}
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }}
                                                at {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="approvers_comment">
                                        </div>
                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif

                            </div>
                            
                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="hods">QA's</label>
                                    <select   @if($document->stage != 1 && !Helpers::userIsQA()) disabled @endif id="choices-multiple-remove-button" class="choices-multiple-approver" {{ !Helpers::userIsQA() ? Helpers::isRevised($document->stage) : ''}} 
                                        name="qa[]" placeholder="Select QA's" multiple>
                                        @foreach ($qa as $hod)
                                                <option value="{{ $hod->id }}"
                                                    @if ($document->qa) @php
                                                   $data = explode(",",$document->qa);
                                                    $count = count($data);
                                                    $i=0;
                                                @endphp
                                                @for ($i = 0; $i < $count; $i++)
                                                    @if ($data[$i] == $hod->id)
                                                     selected @endif
                                                @endfor>
                                            {{ $hod->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Approvers' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                {{-- <p id="approverError" style="color:red">**Approvers are required</p> --}}

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    {{-- Add Comment  --}}
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }}
                                                at {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="approvers_comment">
                                        </div>
                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif

                            </div>

                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="reviewers">Reviewers</label>
                                    <select   @if($document->stage != 1 && !Helpers::userIsQA() ) disabled @endif id="choices-multiple-remove-button" class="choices-multiple-reviewer" {{ !Helpers::userIsQA() ? Helpers::isRevised($document->stage) : ''}} 
                                        name="reviewers[]" placeholder="Select Reviewers" multiple>
                                        @if (!empty($reviewer))
                                            @foreach ($reviewer as $lan)
                                            @if(Helpers::checkUserRolesreviewer($lan))
                                                <option value="{{ $lan->id }}"
                                                    @if ($document->reviewers) @php
                                                   $data = explode(",",$document->reviewers);
                                                    $count = count($data);
                                                    $i=0;
                                                @endphp
                                                @for ($i = 0; $i < $count; $i++)
                                                    @if ($data[$i] == $lan->id)
                                                     selected @endif
                                                    @endfor
                                            @endif>
                                            {{ $lan->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                        @endif
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Reviewers' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                <p id="reviewerError" style="color:red">**Reviewers are required</p>

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    {{-- Add Comment  --}}
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }}
                                                at {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="reviewers_comment">
                                        </div>
                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif

                            </div>

                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="approvers">Approvers</label>
                                    <select   @if($document->stage != 1 && !Helpers::userIsQA()) disabled @endif id="choices-multiple-remove-button" class="choices-multiple-approver" {{ !Helpers::userIsQA() ? Helpers::isRevised($document->stage) : ''}} 
                                        name="approvers[]" placeholder="Select Approvers" multiple>
                                        @if (!empty($approvers))
                                            @foreach ($approvers as $lan)
                                            @if(Helpers::checkUserRolesApprovers($lan))
                                                <option value="{{ $lan->id }}"
                                                    @if ($document->approvers) @php
                                                   $data = explode(",",$document->approvers);
                                                    $count = count($data);
                                                    $i=0;
                                                @endphp
                                                @for ($i = 0; $i < $count; $i++)
                                                    @if ($data[$i] == $lan->id)
                                                     selected @endif
                                                    @endfor
                                            @endif>
                                            {{ $lan->name }}
                                            </option>
                                            @endif
                                        @endforeach
                                        @endif
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if (
                                            $tempHistory->activity_type == 'Approvers' &&
                                                !empty($tempHistory->comment)  &&
                                                $tempHistory->user_id == Auth::user()->id)
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                                {{-- <p id="approverError" style="color:red">**Approvers are required</p> --}}

                                @if (Auth::user()->role != 3 && $document->stage < 8)
                                    {{-- Add Comment  --}}
                                    <div class="comment">
                                        <div>
                                            <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }}
                                                at {{ date('d-M-Y h:i:s') }}</p>

                                            <input class="input-field" type="text" name="approvers_comment">
                                        </div>
                                        <div class="button">Add Comment</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="orig-head">
                        Initiator Information
                    </div>
                    <div class="input-fields row">
                        <div class="col-12">
                            <div class="group-input">
                                <label for="QA Initial Attachments">Initiatal Attachments</label>
                                <div><small class="text-primary">Please Attach all relevant or supporting
                                        documents</small></div>
                                <div class="file-attachment-field">
                                    <div class="file-attachment-list" id="initial_attachments">
                                        @if ($document->initial_attachments)
                                            @foreach (json_decode($document->initial_attachments) as $file)
                                                <h6 type="button" class="file-container text-dark"
                                                    style="background-color: rgb(243, 242, 240);">
                                                    <b>{{ $file }}</b>
                                                    <a href="{{ asset('upload/' . $file) }}"
                                                        target="_blank"><i class="fa fa-eye text-primary"
                                                            style="font-size:20px; margin-right:-10px;"></i></a>
                                                    <a type="button" class="remove-file"
                                                    data-remove-id="initial_attachmentsFile-{{ $loop->index }}"

                                                        data-file-name="{{ $file }}"><i
                                                            class="fa-solid fa-circle-xmark"
                                                            style="color:red; font-size:20px;"></i></a>
                                                </h6>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="add-btn">
                                        <div onclick="document.getElementById('myfile').click()">Add</div>
                                        <input type="file" id="myfile"
                                            name="initial_attachments[]" {{Helpers::isRevised($document->stage)}}
                                            oninput="addMultipleFiles(this, 'initial_attachments')" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 warehouse">
                            <div class="group-input">
                                <label for="Warehousefeedback">Initiated By</label>
                                <input readonly type="text" name="initiated_by" value="{{Helpers::getInitiatorName($document->initiated_by)}}" id="initiated_by">

                            </div>
                        </div>

                        <div class="col-lg-6 new-date-data-field warehouse">
                            <div class="group-input input-date">
                                <label for="initiated On" style="font-weight: 100">Initiated On</label>
                                <div class="calenderauditee">
                                    <input type="text" id="initiated_on" value="{{Helpers::getdateFormat($document->initiated_on)}}" readonly placeholder="DD-MM-YYYY" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="button-block">
                        <button type="submit" name="submit" value="save" id="DocsaveButton"
                            class="saveButton">Save</button>
                        <button type="button" class="nextButton" id="DocnextButton">Next</button>
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
                                    <label for="comments">Author Remarks <span @if (in_array(Auth::user()->id, explode(",", $document->drafters)) && $document->stage == 2)  @else style="display: none" @endif class="text-danger">*</span></label>
                                    <textarea {{Helpers::isRevised($document->stage)}} @if (in_array(Auth::user()->id, explode(",", $document->drafters)) && $document->stage == 2) required @else readonly @endif name="drafter_remarks">{{$document->drafter_remarks}}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="QA Initial Attachments">Author Attachments</label>
                                    <div><small class="text-primary">Please Attach all relevant or supporting
                                            documents</small></div>
                                    <div class="file-attachment-field">
                                        <div class="file-attachment-list" id="drafter_attachments">
                                            @if ($document->drafter_attachments)
                                                @foreach (json_decode($document->drafter_attachments) as $file)
                                                    <h6 type="button" class="file-container text-dark"
                                                        style="background-color: rgb(243, 242, 240);">
                                                        <b>{{ $file }}</b>
                                                        <a href="{{ asset('upload/' . $file) }}"
                                                            target="_blank"><i class="fa fa-eye text-primary"
                                                                style="font-size:20px; margin-right:-10px;"></i></a>
                                                        <a type="button" class="remove-file"
                                                        data-remove-id="drafter_attachmentsFile-{{ $loop->index }}"

                                                            data-file-name="{{ $file }}"><i
                                                                class="fa-solid fa-circle-xmark"
                                                                style="color:red; font-size:20px;"></i></a>
                                                    </h6>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="add-btn">
                                            <div onclick="document.getElementById('myfile1').click()">Add</div>
                                            <input {{ $document->stage == 2 ? '' : 'disabled'}} type="file" id="myfile1"
                                                name="drafter_attachments[]" {{Helpers::isRevised($document->stage)}}
                                                oninput="addMultipleFiles(this, 'drafter_attachments')" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 warehouse">
                                <div class="group-input">
                                    <label for="Warehousefeedback">Author By</label>
                                    <input readonly type="text" name="drafted_by" id="drafted_by" value="{{Helpers::getInitiatorName($document->drafted_by)}}">

                                </div>
                            </div>

                            <div class="col-lg-6 new-date-data-field warehouse">
                                <div class="group-input input-date">
                                    <label for="Drafted On" style="font-weight: 100;">Author On</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="drafted_on" value="{{Helpers::getdateFormat($document->drafted_on)}}" disabled placeholder="DD-MM-YYYY" />
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

                <div id="123" class="tabcontent">
                    <div class="orig-head">
                        Approver Input
                    </div>
                    <div class="input-fields">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="group-input">
                                    <label for="comments">Approver Remarks <span @if (in_array(Auth::user()->id, explode(",", $document->approvers)) && $document->stage == 6)  @else style="display: none" @endif class="text-danger">*</span></label>
                                    <textarea {{Helpers::isRevised($document->stage)}} @if (in_array(Auth::user()->id, explode(",", $document->approvers)) && $document->stage == 6) required @else readonly @endif  name="approver_remarks">{{$document->approver_remarks}}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="QA Initial Attachments">approver Attachments</label>
                                    <div><small class="text-primary">Please Attach all relevant or supporting
                                            documents</small></div>
                                    <div class="file-attachment-field">
                                        <div class="file-attachment-list" id="approver_attachments">
                                            @if ($document->approver_attachments)
                                                @foreach (json_decode($document->approver_attachments) as $file)
                                                    <h6 type="button" class="file-container text-dark"
                                                        style="background-color: rgb(243, 242, 240);">
                                                        <b>{{ $file }}</b>
                                                        <a href="{{ asset('upload/' . $file) }}"
                                                            target="_blank"><i class="fa fa-eye text-primary"
                                                                style="font-size:20px; margin-right:-10px;"></i></a>
                                                        <a type="button" class="remove-file"
                                                        data-remove-id="approver_attachmentsFile-{{ $loop->index }}"

                                                            data-file-name="{{ $file }}"><i
                                                                class="fa-solid fa-circle-xmark"
                                                                style="color:red; font-size:20px;"></i></a>
                                                    </h6>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="add-btn">
                                            <div onclick="document.getElementById('myfile5').click()">Add</div>
                                            <input {{ $document->stage == 6 ? '' : 'disabled'}} type="file" id="myfile5"
                                                name="approver_attachments[]"{{ $document->stage == 0 || $document->stage == 13 ? 'disabled' : '' }}
                                                oninput="addMultipleFiles(this, 'approver_attachments')" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 warehouse">
                                <div class="group-input">
                                    <label for="Warehousefeedback">Approver Completed By</label>
                                    <input readonly type="text" name="approver_by" id="approver_by"  value="{{Helpers::getInitiatorName($document->approver_by)}}">

                                </div>
                            </div>

                            <div class="col-lg-6 new-date-data-field warehouse">
                                <div class="group-input input-date">
                                    <label for="QA Completed On"  style="font-weight: 100;">Approver Completed On</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="approver_on" value="{{Helpers::getdateFormat($document->approver_on)}}" disabled placeholder="DD-MM-YYYY" />  
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

                <div id="456" class="tabcontent">
                    <div class="orig-head">
                        Reviewer Input
                    </div>
                    <div class="input-fields">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="group-input">
                                    <label for="comments">Reviewer Remarks <span @if (in_array(Auth::user()->id, explode(",", $document->reviewers)) && $document->stage == 5)  @else style="display: none" @endif class="text-danger">*</span></label>
                                    <textarea {{Helpers::isRevised($document->stage)}} @if (in_array(Auth::user()->id, explode(",", $document->reviewers)) && $document->stage == 5) required @else readonly @endif  name="reviewer_remarks">{{$document->reviewer_remarks}}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="QA Initial Attachments">Reviewer Attachments</label>
                                    <div><small class="text-primary">Please Attach all relevant or supporting
                                            documents</small></div>
                                    <div class="file-attachment-field">
                                        <div class="file-attachment-list" id="reviewer_attachments">
                                            @if ($document->reviewer_attachments)
                                                @foreach (json_decode($document->reviewer_attachments) as $file)
                                                    <h6 type="button" class="file-container text-dark"
                                                        style="background-color: rgb(243, 242, 240);">
                                                        <b>{{ $file }}</b>
                                                        <a href="{{ asset('upload/' . $file) }}"
                                                            target="_blank"><i class="fa fa-eye text-primary"
                                                                style="font-size:20px; margin-right:-10px;"></i></a>
                                                        <a type="button" class="remove-file"
                                                        data-remove-id="reviewer_attachmentsFile-{{ $loop->index }}"

                                                            data-file-name="{{ $file }}"><i
                                                                class="fa-solid fa-circle-xmark"
                                                                style="color:red; font-size:20px;"></i></a>
                                                    </h6>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="add-btn">
                                            <div onclick="document.getElementById('myfile4').click()">Add</div>
                                            <input type="file" id="myfile4" {{ $document->stage == 5 ? '' : 'disabled'}}
                                                name="reviewer_attachments[]"{{ $document->stage == 0 || $document->stage == 13 ? 'disabled' : '' }}
                                                oninput="addMultipleFiles(this, 'reviewer_attachments')" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 warehouse">
                                <div class="group-input">
                                    <label for="Warehousefeedback">Reviewer Completed By</label>
                                    <input readonly type="text" name="reviewer_by" id="reviewer_by"  value="{{Helpers::getInitiatorName($document->reviewer_by)}}">

                                </div>
                            </div>

                            <div class="col-lg-6 new-date-data-field warehouse">
                                <div class="group-input input-date">
                                    <label for="QA Completed On"  style="font-weight: 100;">Reviewer Completed On</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="reviewer_on" value="{{Helpers::getdateFormat($document->reviewer_on)}}" readonly placeholder="DD-MM-YYYY" />
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
                                    <label for="comments">HODs Remarks <span @if (in_array(Auth::user()->id, explode(",", $document->hods)) && $document->stage == 3)  @else style="display: none" @endif class="text-danger">*</span></label>
                                    <textarea {{Helpers::isRevised($document->stage)}} @if (in_array(Auth::user()->id, explode(",", $document->hods)) && $document->stage == 3) required @else readonly @endif  name="hod_remarks">{{$document->hod_remarks}}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="QA Initial Attachments">HODs Attachments</label>
                                    <div><small class="text-primary">Please Attach all relevant or supporting
                                            documents</small></div>
                                    <div class="file-attachment-field">
                                        <div class="file-attachment-list" id="hod_attachments">
                                            @if ($document->hod_attachments)
                                                @foreach (json_decode($document->hod_attachments) as $file)
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
                                            <div onclick="document.getElementById('myfile2').click()">Add</div>
                                            <input type="file" id="myfile2" {{ $document->stage == 3 ? '' : 'disabled'}}
                                                name="hod_attachments[]"{{ $document->stage == 0 || $document->stage == 13 ? 'disabled' : '' }}
                                                oninput="addMultipleFiles(this, 'hod_attachments')" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 warehouse">
                                <div class="group-input">
                                    <label for="Warehousefeedback">HODs Completed By</label>
                                    <input readonly type="text" name="hod_by" id="hod_by"  value="{{Helpers::getInitiatorName($document->hod_by)}}">

                                </div>
                            </div>

                            <div class="col-lg-6 new-date-data-field warehouse">
                                <div class="group-input input-date">
                                    <label for="HODs Completed On"  style="font-weight: 100;">HODs Completed On</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="hod_on" readonly value="{{Helpers::getdateFormat($document->hod_on)}}" placeholder="DD-MM-YYYY" />
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
                                    <label for="comments">QA Remarks <span @if (in_array(Auth::user()->id, explode(",", $document->qa)) && $document->stage == 4)  @else style="display: none" @endif class="text-danger">*</span></label>
                                    <textarea {{Helpers::isRevised($document->stage)}} @if (in_array(Auth::user()->id, explode(",", $document->qa)) && $document->stage == 4) required @else readonly @endif  name="qa_remarks">{{$document->qa_remarks}}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="QA Initial Attachments">QA Attachments</label>
                                    <div><small class="text-primary">Please Attach all relevant or supporting
                                            documents</small></div>
                                    <div class="file-attachment-field">
                                        <div class="file-attachment-list" id="qa_attachments">
                                            @if ($document->qa_attachments)
                                                @foreach (json_decode($document->qa_attachments) as $file)
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
                                            <div onclick="document.getElementById('myfile3').click()">Add</div>
                                            <input type="file" id="myfile3" {{ $document->stage == 4 ? '' : 'disabled'}}
                                                name="qa_attachments[]" {{ $document->stage == 0 || $document->stage == 13 ? 'disabled' : '' }}
                                                oninput="addMultipleFiles(this, 'qa_attachments')" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 warehouse">
                                <div class="group-input">
                                    <label for="Warehousefeedback">QA Completed By</label>
                                    <input readonly type="text" name="qa_by" id="qa_by"  value="{{Helpers::getInitiatorName($document->qa_by)}}">

                                </div>
                            </div>

                            <div class="col-lg-6 new-date-data-field warehouse">
                                <div class="group-input input-date">
                                    <label for="QA Completed On"  style="font-weight: 100;">QA Completed On</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="qa_on" readonly value="{{Helpers::getdateFormat($document->qa_on)}}" placeholder="DD-MM-YYYY" />
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

                
 <!-- ------------------------------------------------------------------------------------------------------------- -->
                <div id="add-doc" class="tabcontent">
                    <div class="orig-head">
                        Training Information
                    </div>
                    <div class="input-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="train-require">Training Required?</label>
                                    <select name="training_required" {{Helpers::isRevised($document->stage)}}  required>
                                        <option value="">Enter your Selection</option>
                                        @if ($document->training_required == 'yes')
                                            <option value="yes" selected>Yes</option>
                                            <option value="no">No</option>
                                        @else
                                            <option value="no" selected>No</option>
                                            <option value="yes">Yes</option>
                                            
                                        @endif

                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Training Required' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="link-doc">Trainer</label>
                                    <select name="trainer" {{Helpers::isRevised($document->stage)}} >
                                        <option value="" selected>Enter your Selection</option>
                                        @foreach ($trainer as $temp)
                                        @if(Helpers::checkUserRolestrainer($temp))
                                            <option value="{{ $temp->id }}"
                                                @if (!empty($trainingDoc)) @if ($trainingDoc->trainer == $temp->id) selected @endif
                                                @endif>{{ $temp->name }}</option>
                                        @endif        
                                        @endforeach
                                    </select>
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Trainer' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="group-input">
                                    <label for="launch-cbt">Launch CBT</label>
                                    <select name="cbt">
                                        <option value="" selected>Enter your Selection</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="group-input">
                                    <label for="training-type">Type</label>
                                    <select name="training-type">
                                        <option value="" selected>Enter your Selection</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                        <option value="1`">Lorem, ipsum.</option>
                                    </select>
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-12">
                                <div class="group-input">
                                    <label for="test">
                                        Test(0)<button type="button" name="test"
                                            onclick="addTrainRow('test')" {{Helpers::isRevised($document->stage)}}>+</button>
                                    </label>
                                    <table class="table-bordered table" id="test">
                                        <thead>
                                            <tr>
                                                <th class="row-num">Row No.</th>
                                                <th class="question">Question</th>
                                                <th class="answer">Answer</th>
                                                <th class="result">Result</th>
                                                <th class="comment">Comment</th>
                                                <th class="comment">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="test">
                                        Survey(0)<button type="button" name="reporting1"
                                            onclick="addTrainRow('survey')"{{Helpers::isRevised($document->stage)}} >+</button>
                                    </label>
                                    <table class="table-bordered table" id="survey">
                                        <thead>
                                            <tr>
                                                <th class="row-num">Row No.</th>
                                                <th class="question">Subject</th>
                                                <th class="answer">Topic</th>
                                                <th class="result">Rating</th>
                                                <th class="comment">Comment</th>
                                                <th class="comment">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="comments">Comments</label>
                                    <textarea name="comments" {{Helpers::isRevised($document->stage)}} >{{ $document->comments }}</textarea>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="button-block">
                        <button type="submit" name="submit" value="save" class="saveButton">Save</button>
                        <button type="button" class="backButton" onclick="previousStep()">Back</button>
                        <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                    </div>
                </div>

                <div id="doc-content" class="tabcontent">
                    <div class="orig-head">
                        Standard Operating Procedure
                    </div>
                    <div class="input-fields">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="purpose">Objective</label>
                                    <textarea name="purpose" {{Helpers::isRevised($document->stage)}}>{{ $document->document_content ? $document->document_content->purpose : '' }}</textarea>
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Purpose' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="purpose_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="scope">Scope</label>

                                    <textarea name="scope" {{Helpers::isRevised($document->stage)}} >{{ $document->document_content ? $document->document_content->scope : '' }}</textarea>
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Scope' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="scope_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="responsibility" id="responsibility">
                                        Responsibility<button type="button" id="responsibilitybtnadd"
                                            name="button" {{Helpers::isRevised($document->stage)}} >+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    <div id="responsibilitydiv">
                                        @if ($document->document_content && !empty($document->document_content->responsibility))
                                            @foreach (unserialize($document->document_content->responsibility) as $key => $data)
                                                <div class="{{  str_contains($key, 'sub') ? 'subSingleResponsibilityBlock' : 'singleResponsibilityBlock' }}">
                                                    @if (str_contains($key, 'sub'))
                                                        <div class="resrow row">
                                                            <div class="col-6">
                                                                <textarea name="responsibility[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-1">
                                                                <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="row">
                                                            <div class="col-sm-10">
                                                                <textarea name="responsibility[]" class="myclassname" {{Helpers::isRevised($document->stage)}} >{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-dark subResponsibilityAdd">+</button>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="singleResponsibilityBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="responsibility[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subResponsibilityAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Responsibility' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)
                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="responsibility_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="group-input">
                                    
                                    <label for="accountability" id="accountability">
                                        Accountability<button type="button" id="accountabilitybtnadd"
                                            name="button">+</button>
                                            <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    </label>
                                    
                                    <div id="accountabilitydiv">
                                        
                                        @if ($document->document_content && !empty($document->document_content->accountability))
                                            @foreach (unserialize($document->document_content->accountability) as $key => $data)
                                                <div class="{{  str_contains($key, 'sub') ? 'subSingleAccountabilityBlock' : 'singleAccountabilityBlock' }}">
                                                    @if (str_contains($key, 'sub'))
                                                        <div class="resrow row">
                                                            <div class="col-6">
                                                                <textarea name="accountability[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-1">
                                                                <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @else 
                                                        <div class="row">
                                                            <div class="col-sm-10">
                                                                <textarea name="accountability[]" class="myclassname" {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-dark subAccountabilityAdd">+</button>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="group-input">

                                    <label for="references" id="references">
                                        References<button type="button" id="referencesbtadd" name="button" {{Helpers::isRevised($document->stage)}}>+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    
                                    <div id="referencesdiv">
                                        @if ($document->document_content && !empty($document->document_content->references))
                                            @foreach (unserialize($document->document_content->references) as $key => $data)
                                                @if (!empty($data))
                                                    <div class="{{  str_contains($key, 'sub') ? 'subSingleReferencesBlock' : 'singleReferencesBlock' }}">
                                                        @if (str_contains($key, 'sub'))
                                                            <div class="resrow row">
                                                                <div class="col-6">
                                                                    <textarea name="references[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                                </div>
                                                                <div class="col-1">
                                                                    <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                                </div>
                                                            </div>
                                                        @else    
                                                            <div class="row">
                                                                <div class="col-sm-10">
                                                                    <textarea name="references[]" class="myclassname" {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea>
                                                                </div>
                                                                <div class="col-sm-1">
                                                                    <button class="btn btn-dark subReferencesAdd">+</button>
                                                                </div>
                                                                <div class="col-sm-1">
                                                                    <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="singleReferencesBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea name="references[]" class="myclassname"></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subReferencesAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'References' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach

                                   
                                    
                                   
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="references_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="abbreviation" id="abbreviation">
                                        Abbreviation<button type="button" id="abbreviationbtnadd"
                                            name="button" {{Helpers::isRevised($document->stage)}} >+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    
                                    <div id="abbreviationdiv">
                                        @if ($document->document_content && !empty($document->document_content->abbreviation))
                                            @foreach (unserialize($document->document_content->abbreviation) as $key => $data)
                                                <div class="{{  str_contains($key, 'sub') ? 'subSingleAbbreviationBlock' : 'singleAbbreviationBlock' }}">
                                                    @if (str_contains($key, 'sub'))
                                                        <div class="resrow row">
                                                            <div class="col-6">
                                                                <textarea name="abbreviation[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-1">
                                                                <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @else 
                                                        <div class="row">
                                                            <div class="col-sm-10">
                                                                <textarea name="abbreviation[]" class="myclassname" {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-dark subAbbreviationAdd">+</button>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Abbreviation' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="abbreviation_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="abbreviation" id="definition">
                                        Definition<button type="button" id="Definitionbtnadd" name="button" {{Helpers::isRevised($document->stage)}} >+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    
                                    <div id="definitiondiv">
                                        @if ($document->document_content && !empty($document->document_content->defination))
                                            @foreach (unserialize($document->document_content->defination) as $key => $data)
                                                <div class="{{  str_contains($key, 'sub') ? 'subSingleDefinitionBlock' : 'singleDefinitionBlock' }}">
                                                    @if (str_contains($key, 'sub'))
                                                        <div class="resrow row">
                                                            <div class="col-6">
                                                                <textarea name="defination[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-1">
                                                                <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @else 
                                                        <div class="row">
                                                            <div class="col-sm-10">
                                                                <textarea name="defination[]" class="myclassname" {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-dark subDefinitionAdd">+</button>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>    
                                            @endforeach
                                        @endif
                                    </div>

                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Definiton' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="defination_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="reporting" id="newreport">
                                        General Instructions<button type="button" id="materialsbtadd"
                                            name="button" {{Helpers::isRevised($document->stage)}} >+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    @if ($document->document_content && !empty($document->document_content->materials_and_equipments))
                                        <div class="materialsBlock">
                                            @foreach (unserialize($document->document_content->materials_and_equipments) as $key => $data)
                                                <div class="{{  str_contains($key, 'sub') ? 'subSingleMaterialBlock' : 'singleMaterialBlock' }}" >
                                                    @if (str_contains($key, 'sub'))
                                                        <div class="resrow row">
                                                            <div class="col-6">
                                                                <textarea name="materials_and_equipments[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-1">
                                                                <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="row">
                                                            <div class="col-sm-10">
                                                                <textarea name="materials_and_equipments[]" class="myclassname" {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea> 
                                                            </div>

                                                            <div class="col-sm-1">
                                                                <button type="button" class="subMaterialsAdd" name="button" {{Helpers::isRevised($document->stage)}} >+</button>
                                                            </div>

                                                            <div class="col-sm-1">
                                                                <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="singleMaterialBlock">
                                            <div class="row">
                                                <div class="col-sm-10">
                                                    <textarea name="materials_and_equipments[]" class="myclassname"></textarea>
                                                </div>

                                                <div class="col-sm-1">
                                                    <button type="button" class="subMaterialsAdd" name="button" {{Helpers::isRevised($document->stage)}} >+</button>
                                                </div>

                                                <div class="col-sm-1">
                                                    <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div id="materialsdiv"></div>
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Materials and Equipments' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text"
                                            name="materials_and_equipments_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif


                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="procedure">Procedure</label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    <textarea name="procedure" id="summernote" class="tiny">{{ $document->document_content ? $document->document_content->procedure : '' }}</textarea>
                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Procedure' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            

                            <div class="col-md-12">
                                <div class="group-input">
                                    <label for="reporting" id="newreport">
                                        Cross References<button type="button" id="reportingbtadd" name="button" {{Helpers::isRevised($document->stage)}}>+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>

                                    <div id="reportingdiv">
                                        @if ($document->document_content && !empty($document->document_content->reporting))
                                            @foreach (unserialize($document->document_content->reporting) as $key => $data)
                                                <div class="{{  str_contains($key, 'sub') ? 'subSingleReportingBlock' : 'singleReportingBlock' }}">
                                                    @if (str_contains($key, 'sub'))
                                                        <div class="resrow row">
                                                            <div class="col-6">
                                                                <textarea name="reporting[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                            </div>
                                                            <div class="col-1">
                                                                <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @else 
                                                        <div class="row">
                                                            <div class="col-sm-10">
                                                                <textarea type="text" name="reporting[]" class=""
                                                                {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea>
                                                        </div>
                                                        <div class="col-sm-1">
                                                                <button class="btn btn-dark subReportingAdd">+</button>
                                                            </div>
                                                        <div class="col-sm-1">
                                                            <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                        </div>
                                                        </div>
                                                    @endif 
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="singleReportingBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <textarea type="text" name="reporting[]" class=""></textarea>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subReportingAdd">+</button>
                                                    </div>
                                                <div class="col-sm-1">
                                                    <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'Reporting' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="reporting_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif


                            {{-- <div class="col-md-12">   --Aditya
                                <div class="group-input">
                                    <label for="annexure">
                                        Annexure<button type="button" name="ann" id="annexurebtnadd">+</button>
                                    </label>
                                    <div><small class="text-primary">Please mention brief summary</small></div>
                                    <table class="table-bordered table" id="annexure">
                                        <thead>

                                            <tr>
                                                <th class="sr-num">Sr. No.</th>
                                                <th class="annx-num">Annexure No.</th>
                                                <th class="annx-title">Title of Annexure</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            @if (!empty($annexure))
                                                @foreach (unserialize($annexure->sno) as $key => $data)
                                                    <tr>
                                                        <td><input type="text" name="serial_number[]"
                                                                value="{{ $data }}"></td>
                                                        <td><input type="text" name="annexure_number[]"
                                                                value="{{ unserialize($annexure->annexure_no)[$key] }}">
                                                        </td>
                                                        <td><input type="text" name="annexure_data[]"
                                                                value="{{ unserialize($annexure->annexure_title)[$key] }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <div id="annexurediv"></div>
                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="group-input">

                                    <label for="ann" id="ann">
                                        Annexure<button type="button" id="annbtadd" name="button" {{Helpers::isRevised($document->stage)}}>+</button>
                                    </label>
                                    <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                                    
                                    <div id="anndiv">
                                        @if ($document->document_content && !empty($document->document_content->ann))
                                            @foreach (unserialize($document->document_content->ann) as $key => $data)
                                                @if (!empty($data))
                                                    <div class="{{ str_contains($key, 'sub') ? 'subSingleAnnexureBlock' : 'singleAnnexureBlock' }}">
                                                        @if (str_contains($key, 'sub'))
                                                            <div class="resrow row">
                                                                <div class="col-6">
                                                                    <textarea name="ann[{{ $key }}]" class="myclassname">{{ $data }}</textarea>
                                                                </div>
                                                                <div class="col-1">
                                                                    <button class="btn btn-danger abbreviationbtnRemove">Remove</button>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="row">
                                                                <div class="col-sm-10">
                                                                    <textarea name="ann[]" class="myclassname" {{Helpers::isRevised($document->stage)}}>{{ $data }}</textarea>
                                                                </div>
                                                                <div class="col-sm-1">
                                                                    <button class="btn btn-dark subAnnexureAdd">+</button>
                                                                </div>
                                                                <div class="col-sm-1">
                                                                    <button class="btn btn-danger removeAllBlocks">Remove</button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="singleAnnexureBlock">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <input type="text" name="ann[]" class="myclassname">
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-dark subAnnexureAdd">+</button>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn-btn-danger abbreviationbtnRemove">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @foreach ($history as $tempHistory)
                                        @if ($tempHistory->activity_type == 'ann' && !empty($tempHistory->comment) )
                                            @php
                                                $users_name = DB::table('users')
                                                    ->where('id', $tempHistory->user_id)
                                                    ->value('name');
                                            @endphp
                                            <p style="color: blue">Modify by {{ $users_name }} at
                                                {{ $tempHistory->created_at }}
                                            </p>
                                            <input class="input-field"
                                                style="background: #ffff0061;
                                    color: black;"
                                                type="text" value="{{ $tempHistory->comment }}" disabled>
                                        @endif
                                    @endforeach

                                   
                                    
                                   
                                </div>
                            </div>

                            {{-- @if (Auth::user()->role != 3 && $document->stage < 8)
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="ann_comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif --}}
                            {{-- <div class="col-md-12">
                                <div class="group-input">
                                    <label for="test">
                                        Revision History<button type="button" name="reporting2"
                                            onclick="addDocRow('revision')">+</button>
                                    </label>
                                    <div><small class="text-primary">Please mention brief summary</small></div>
                                    <table class="table-bordered table" id="revision">
                                        <thead>
                                            <tr>
                                                <th class="sop-num">SOP Revision No.</th>
                                                <th class="dcrf-num">Change Control No./ DCRF No.</th>
                                                <th class="changes">Changes</th>
                                                //<th class="deleteRow">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}
                            @if (Auth::user()->role != 3 && $document->stage < 8)

                                {{-- Add Comment  --}}
                                <div class="comment">
                                    <div>
                                        <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                            {{ date('d-M-Y h:i:s') }}</p>

                                        <input class="input-field" type="text" name="comment">
                                    </div>
                                    <div class="button">Add Comment</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="button-block">
                        <button type="submit" name="submit" value="save" class="saveButton">Save</button>
                        <button type="button" class="backButton" onclick="previousStep()">Back</button>
                        <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                    </div>
                </div>

                {{-- HOD REMARKS TAB START --}}
                <div id="hod-remarks-tab" class="tabcontent">

                    <div class="input-fields">
                        <div class="group-input">
                            <label for="hod-remark">HOD Comments</label>
                            <textarea class="summernote {{ !Helpers::checkRoles(4) ? 'summernote-disabled' : '' }}" name="hod_comments">{{ $document->document_content ? $document->document_content->hod_comments : '' }}</textarea>
                        </div>
                    </div>

                    <div class="input-fields">
                        <label for="tran-attach">HOD Attachments</label>
                        <div class="file-attachment-field">
                            <div class="file-attachment-list" id="hod_attachments">
                                @if ($document->document_content && $document->document_content->hod_attachments)
                                    @foreach (json_decode($document->document_content->hod_attachments) as $file)
                                        <h6 type="button" class="file-container text-dark"
                                            style="background-color: rgb(243, 242, 240);">
                                            <input type="hidden" name="existing_hod_attachments[{{ $file }}]">
                                            <b>{{ $file }}</b>
                                            <a href="{{ asset('upload/' . $file) }}"
                                                target="_blank"><i class="fa fa-eye text-primary"
                                                    style="font-size:20px; margin-right:-10px;"></i></a>
                                            <a type="button" class="remove-file"
                                                data-file-name="{{ $file }}"><i
                                                    class="fa-solid fa-circle-xmark"
                                                    style="color:red; font-size:20px;"></i></a>
                                        </h6>
                                    @endforeach
                                @endif
                            </div>
                            <div class="add-btn">
                                <div class="{{ !Helpers::checkRoles(4) ? 'btn-disabled' : 'add-hod-attachment-btn' }} ">Add</div>
                                <input type="file" id="myfile" name="hod_attachments[]"
                                class="add-hod-attachment-file"
                                    oninput="addMultipleFiles(this, 'hod_attachments')" multiple>
                            </div>
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

                    <div class="d-flex justify-content-end">
                        <div>
                            <button 
                            data-bs-toggle="modal" data-bs-target="#annexure-modal"
                            type="button" class="btn btn-primary">Annexure Print</button>
                        </div>
                    </div>

                    <div class="input-fields">
                        @if ($document->document_content && !empty($document->document_content->annexuredata))
                            @foreach (unserialize($document->document_content->annexuredata) as $data)
                                <div class="group-input mb-3">
                                    <label>Annexure A-{{ $loop->index + 1 }}</label>
                                    <textarea class="summernote" name="annexuredata[]">{{ $data }}</textarea>
                                </div>
                            @endforeach
                        @else
                            @for ($i = 1; $i <= 20; $i++)
                                <div class="group-input mb-3">
                                    <label for="annexure-{{ $i }}">Annexure A-{{ $i }}</label>
                                    <textarea class="summernote" name="annexuredata[]" id="annexure-{{ $i }}"></textarea>
                                </div>
                            @endfor
                        @endif
                    </div>
                    <div class="button-block">
                        <button type="submit" name="submit" value="save" class="saveButton">Save</button>
                        <button type="button" class="backButton" onclick="previousStep()">Back</button>
                        <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                    </div>
                </div>

                <div id="distribution-retrieval" class="tabcontent">
                    <div class="orig-head">
                        Distribution & Retrieval
                    </div>
                    {{-- <div class="col-md-12 input-fields">
                        <div class="group-input">

                            <label for="distribution" id="distribution">
                                Distribution & Retrieval<button type="button" id="distributionbtnadd" name="button">+</button>
                            </label>
                            <div><small class="text-primary">Please insert "NA" in the data field if it does not require completion</small></div>
                            @if (!empty($document->document_content->distribution))
                                @foreach (unserialize($document->document_content->distribution) as $data)
                                    <input type="text" name="distribution[]" class="myclassname"
                                        value="{{ $data }}">
                                @endforeach
                            @else
                                <input type="text" name="distribution[]" class="myclassname">
                            @endif

                            <div id="distributiondiv"></div>
                            @foreach ($history as $tempHistory)
                                @if ($tempHistory->activity_type == 'distribution' && !empty($tempHistory->comment) )
                                    @php
                                        $users_name = DB::table('users')
                                            ->where('id', $tempHistory->user_id)
                                            ->value('name');
                                    @endphp
                                    <p style="color: blue">Modify by {{ $users_name }} at
                                        {{ $tempHistory->created_at }}
                                    </p>
                                    <input class="input-field"
                                        style="background: #ffff0061;
                            color: black;"
                                        type="text" value="{{ $tempHistory->comment }}" disabled>
                                @endif
                            @endforeach

                           
                            
                           
                        </div>
                    </div>

                    @if (Auth::user()->role != 3 && $document->stage < 8)

                        {{-- Add Comment 
                        <div class="comment">
                            <div>
                                <p class="timestamp" style="color: blue">Modify by {{ Auth::user()->name }} at
                                    {{ date('d-M-Y h:i:s') }}</p>

                                <input class="input-field" type="text" name="distribution_comment">
                            </div>
                            <div class="button">Add Comment</div>
                        </div>
                    @endif --}}
                    <div class="input-fields">
                        <div class="group-input">
                            <label for="distriution_retrieval">
                                Distribution & Retrieval
                                <button type="button" name="agenda"
                                    onclick="addDistributionRetrieval('distribution-retrieval-grid')">+</button>
                            </label>
                            <div class="table-responsive retrieve-table">
                                <table class="table table-bordered" id="distribution-retrieval-grid">
                                    <thead>
                                        <tr>
                                            <th>Row </th>
                                            <th  class="copy-name">Document Title</th>
                                            <th class="copy-name">Document Number</th>
                                            <th class="copy-name">Document Printed By</th>
                                            <th class="copy-name">Document Printed on</th>
                                            <th class="copy-num">Number of Print Copies</th>
                                            <th class="copy-name">Issuance Date</th>
                                            <th class="copy-name">Issued To </th>
                                            <th class="copy-long">Department/Location</th>
                                            <th class="copy-num">Number of Issued Copies</th>
                                            <th class="copy-long">Reason for Issuance</th>
                                            <th class="copy-name">Retrieval Date</th>
                                            <th class="copy-name">Retrieved By</th>
                                            <th class="copy-name">Retrieved Person Department</th>
                                            <th class="copy-num">Number of Retrieved Copies</th>
                                            <th class="copy-long">Reason for Retrieval</th>
                                            <th class="copy-long">Remarks</th>
                                            <th class="copy-long">Action</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php        
                                        $doc_number = '';                        
                                            $doc_number = Helpers::getDivisionName($document->division_id)
                                                        . '/' . ($document->document_type_name ? $temp . ' /' : '')
                                                        . $document->created_at->format('Y')
                                                        . '/000' . $document->id . 'R1.0';
                                        @endphp
                                        @foreach ($document_distribution_grid as $grid)
                                            <tr>
                                                <td>
                                                    {{ $loop->index + 1 }}
                                                    {{-- <input type="text" value="{{ $loop->index }}" name="distribution[{{ $loop->index }}][serial_number]"> --}}
                                                </td>
                                                <td><input  type="text" value="{{ $grid->document_name }}"  name="distribution[{{ $loop->index }}][document_name]"> 
                                                </td>
                                                
                                                <td><input type="text" name="distribution[{{ $loop->index }}][document_name]" value="{{ $doc_number }}">
                                                </td>
                                                <td><input type="text" value="{{ Helpers::getInitiatorName($grid->user_id) }}" name="distribution[{{ $loop->index }}][Helpers::getInitiatorName($grid->user_id)]">
                                                </td>
                                                <td><input type="text" value="{{ Helpers::getdateFormat($grid->created_at) }}" name="distribution[{{ $loop->index }}][Helpers::getdateFormat($grid->created_at)]">
                                                </td>
                                                <td><input type="text" value="{{ $grid->issue_copies }}" name="distribution[{{ $loop->index }}][issue_copies]">
                                                </td>
                                                <td><div class="group-input new-date-document_distribution_grid-field mb-0">
                                                <div class="input-date "><div
                                                    class="calenderauditee">
                                                <input type="text" id="issuance_date' + serialNumber +'" readonly placeholder="DD-MMM-YYYY" value="{{ Helpers::getdateFormat($grid->created_at) }}"/>
                                                <input type="date" name="distribution[{{ $loop->index }}][issuance_date]" 
                                                class="hide-input" style="position: absolute; top: 0; left: 0; opacity: 0;"
                                                oninput="handleDateInput(this, `issuance_date' + serialNumber +'`)" value="{{ Helpers::getdateFormat($grid->created_at) }}"/></div></div></div>
                                            </td>
                                            
                                                <td>
                                                    <select id="select-state" placeholder="Select..."
                                                        name="distribution[{{ $loop->index }}][issuance_to]" >
                                                        <option value='0' {{ $grid->issuance_to == '0' ? 'selected' : '' }}>-- Select --</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}" {{ $grid->issuance_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="select-state" placeholder="Select..."
                                                        name="distribution[{{ $loop->index }}][location]">
                                                        <option value='0' {{ $grid->location == '0' ? 'selected' : '' }}>-- Select --</option>
                                                        @foreach ($departments as $department)
                                                            <option 
                                                                value='{{ $department->id }}' {{ $grid->retrieved_department == $department->id ? 'selected' : '' }}>
                                                                {{ $department->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>    
                                            <td><input type="text" name="distribution[{{ $loop->index }}][issue_copies]" value="{{ $grid->issue_copies }}">
                                            </td>
                                            <td><input type="text" name="distribution[{{ $loop->index }}][print_reason]" value="{{ $grid->print_reason }}">
                                            </td>
                                            <td><div class="group-input new-date-data-field mb-0">
                                                <div class="input-date "><div
                                                    class="calenderauditee">
                                                <input type="text" id="retrieval_date' + serialNumber +'" readonly placeholder="DD-MMM-YYYY" value="{{ $grid->retrieval_date }}"/>
                                                <input type="date" name="distribution[{{ $loop->index }}][retrieval_date]" class="hide-input" 
                                                oninput="handleDateInput(this, `retrieval_date' + serialNumber +'`)" value="{{ $grid->retrieval_date }}"/></div></div></div>
                                            </td>
                                            <td>
                                                <select id="select-state" placeholder="Select..."
                                                    name="distribution[{{ $loop->index }}][retrieval_by]">
                                                    <option value="" {{ $grid->retrieval_by == '' ? 'selected' : '' }}>Select a value</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" {{ $grid->retrieval_by == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select id="select-state" placeholder="Select..."
                                                    name="distribution[{{ $loop->index }}][retrieved_department]">
                                                    <option value='0' {{ $grid->retrieved_department == '0' ? 'selected' : '' }}>-- Select --</option>
                                                    @foreach ($departments as $department)
                                                        <option 
                                                            value='{{ $department->id }}' {{ $grid->retrieved_department == $department->id ? 'selected' : '' }}>
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>    
                                            <td><input type="number" name="distribution[{{ $loop->index }}][retrieved_copies]" value="{{ $grid->retrieved_copies }}">
                                            </td>
                                            <td><input type="text" name="distribution[{{ $loop->index }}][retrieved_reason]" value="{{ $grid->retrieved_reason }}">
                                            </td>
                                            <td><input type="text" name="distribution[{{ $loop->index }}][remark]" value="{{ $grid->remark }}">
                                            </td>
                                            <td>
                                                <button class='removeTrainRow'>Remove</button>
                                            </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="button-block">
                        <button type="submit" name="submit" value="save" class="saveButton">Save</button>
                        <button type="button" class="backButton" onclick="previousStep()">Back</button>
                        <button type="button" class="nextButton" onclick="nextStep()">Next</button>
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
                                            <th class="person">Groupd</th>
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
                                            <th class="person">Groups</th>
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
                        </div>
                    </div>
                    <div class="button-block">
                        <button type="submit" name="submit" value="save" class="saveButton">Save</button>
                        <button type="button" class="backButton" onclick="previousStep()">Back</button>
                        <button type="button" class="nextButton" onclick="nextStep()">Next</button>
                    </div>
                </div> --}}

                <div id="sign" class="tabcontent">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Originated By : {{Helpers::getInitiatorName($document->originator_id)}}
                                </div>
                                {{-- @php
                                    $inreview = DB::table('stage_manages')
                                        ->join('users', 'stage_manages.user_id', '=', 'users.id')
                                        ->select('stage_manages.*', 'users.name as user_name')
                                        ->where('document_id', $document->id)
                                        ->where('stage', 'In-Review')
                                        ->get();

                                @endphp
                                    <div class="name">{{ $document->originator ? $document->originator->name : 'null' }}</div> --}}

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Originated On 
                                </div>
                                <div class="name">{{ $document->created_at }}</div>
                            </div>

                        </div>

                        {{-- <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Originated On 
                                </div>
                                @php
                                    $inreview = DB::table('stage_manages')
                                        ->join('users', 'stage_manages.user_id', '=', 'users.id')
                                        ->select('stage_manages.*', 'users.name as user_name')
                                        ->where('document_id', $document->id)
                                        ->where('stage', 'In-Approval')
                                        ->where('deleted_at', null)
                                        ->get();

                                @endphp
                                @foreach ($inreview as $temp)
                                    <div class="name">{{ $temp->user_name }}</div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Document Reuqest Approved On
                                </div>
                                <div class="name">{{ $temp->created_at }}</div>
                                @endforeach
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Document Writing Completed By
                                </div>
                                @php
                                    $inreview = DB::table('stage_manages')
                                        ->join('users', 'stage_manages.user_id', '=', 'users.id')
                                        ->select('stage_manages.*', 'users.name as user_name')
                                        ->where('document_id', $document->id)
                                        ->where('stage', 'In-Approval')
                                        ->get();

                                @endphp
                                @foreach ($inreview as $temp)
                                    <div class="name">{{ $temp->user_name }}</div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Document Writing Completed On
                                </div>
                                <div class="name">{{ $temp->created_at }}</div>
                                @endforeach
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    HOD Review By
                                </div>
                                @php
                                    $inhodreview = DB::table('stage_manages')
                                        ->join('users', 'stage_manages.user_id', '=', 'users.id')
                                        ->select('stage_manages.*', 'users.name as user_name')
                                        ->where('document_id', $document->id)
                                        ->where('stage', 'HOD Review-Submit')
                                        ->where('deleted_at', null)
                                        ->get();

                                @endphp
                                @foreach ($inhodreview as $temp)
                                    <div class="name">{{ $temp->user_name }}</div>
                                @endforeach

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    HOD Reviewed On
                                </div>
                                @foreach ($inhodreview as $temp)
                                    <div class="name">{{ $temp->created_at }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Reviewd By
                                </div>
                                @php
                                    $inreview = DB::table('stage_manages')
                                        ->join('users', 'stage_manages.user_id', '=', 'users.id')
                                        ->select('stage_manages.*', 'users.name as user_name')
                                        ->where('document_id', $document->id)
                                        ->where('stage', 'Review-Submit')
                                        ->where('deleted_at', null)
                                        ->get();

                                @endphp
                                @foreach ($inreview as $temp)
                                    <div class="name">{{ $temp->user_name }}</div>
                                @endforeach

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Reviewed On
                                </div>
                                @foreach ($inreview as $temp)
                                    <div class="name">{{ $temp->created_at }}</div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Approved By
                                </div>
                                @php
                                    $inreview = DB::table('stage_manages')
                                        ->join('users', 'stage_manages.user_id', '=', 'users.id')
                                        ->select('stage_manages.*', 'users.name as user_name')
                                        ->where('document_id', $document->id)
                                        ->where('stage', 'Approval-Submit')
                                        ->where('deleted_at', null)
                                        ->get();

                                @endphp
                                @foreach ($inreview as $temp)
                                    <div class="name">{{ $temp->user_name }}</div>
                                @endforeach

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Approved On
                                </div>
                                @foreach ($inreview as $temp)
                                    <div class="name">{{ $temp->created_at }}</div>
                                @endforeach
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Training Completed By
                                </div>
                                <div class="name">Amit Patel</div>
                                <div class="name">Amit Patel</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="review-names">
                                <div class="orig-head">
                                    Training Completed On
                                </div>
                                <div class="name">29-12-2023 11:12PM</div>
                                <div class="name">29-12-2023 11:12PM</div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="button-block">
                        <button type="submit" name="submit" value="save" class="saveButton">Save</button>
                        <button type="button" class="backButton" onclick="previousStep()">Back</button>
                        <button type="submit">Submit</button>
                    </div>
                </div>

                @if ($document->stage < 8)
                    {{-- <div class="form-btn-bar">
                        <div class="container-fluid header-bottom bottom-pr-links">
                            <div class="container">
                                <div class="bottom-links">
                                    <div>
                                        <button type="submit" name="submit" value="save">Save</button>
                                    </div>
                                    <div>
                                        <a href="{{ route('documents.index') }}"> <button
                                                type="submit">Cancel</button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                @endif

            </form>
        </div>
    </div>

    <div class="modal fade" id="annexure-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Annexure Print</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('document.print.pdf', $document->id) }}" method="GET" target="_blank">
                    @csrf
                    <!-- Modal body -->
                    <div class="modal-body">
                        @for ($i = 1; $i <= 20; $i++)
                            <a href='{{ route('document.print.annexure', ['document' => $document->id, 'annexure' => $i]) }}' target="_blank">Print Annexure A-{{ $i }}</a> <br>
                        @endfor
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


    <style>
        #step-form>div {
            display: none
        }

        #step-form>div:nth-child(1) {
            display: block;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const removeButtons = document.querySelectorAll('.remove-file');

            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const fileName = this.getAttribute('data-file-name');
                    const fileContainer = this.closest('.file-container');

                    // Hide the file container
                    if (fileContainer) {
                        fileContainer.remove()
                    }
                });
            });
        });
    </script>

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

    {{-- <script>
        var editor = new FroalaEditor('.summernote', {
            key: "uXD2lC7C4B4D4D4J4B11dNSWXf1h1MDb1CF1PLPFf1C1EESFKVlA3C11A8D7D2B4B4G2D3J3==",
            imageUploadParam: 'image_param',
            imageUploadMethod: 'POST',
            imageMaxSize: 20 * 1024 * 1024,
            imageUploadURL: "{{ route('api.upload.file') }}",
            fileUploadParam: 'image_param',
            fileUploadURL: "{{ route('api.upload.file') }}",
            videoUploadParam: 'image_param',
            videoUploadURL: "{{ route('api.upload.file') }}",
            videoMaxSize: 500 * 1024 * 1024,
        });

        
        $(".summernote-disabled").FroalaEditor("edit.off");
    </script> --}}
    <script>
        VirtualSelect.init({
            ele: '#reference_record, #notify_to, #cc_reference_record'
        });

        // $('#summernote').summernote({
        //     toolbar: [
        //         ['style', ['style']],
        //         ['font', ['bold', 'underline', 'clear', 'italic']],
        //         ['color', ['color']],
        //         ['para', ['ul', 'ol', 'paragraph']],
        //         ['table', ['table']],
        //         ['insert', ['link', 'picture', 'video']],
        //         ['view', ['fullscreen', 'codeview', 'help']]
        //     ]
        // });

        // $('.summernote').summernote({
        //     toolbar: [
        //         ['style', ['style']],
        //         ['font', ['bold', 'underline', 'clear', 'italic']],
        //         ['color', ['color']],
        //         ['para', ['ul', 'ol', 'paragraph']],
        //         ['table', ['table']],
        //         ['insert', ['link', 'picture', 'video']],
        //         ['view', ['fullscreen', 'codeview', 'help']]
        //     ]
        // });
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

            $(document).on('click', '.close-icon', function() {
                var thisValue = $(this).parent().text().trim().slice(0, -1).trim(); // Remove the '×' from the value
                $(this).closest('li').remove();
                $('#keywords option').filter(function() {
                    return $(this).text().trim() === thisValue;
                }).remove()
            })
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

        $('.add-hod-attachment-btn').click(function() {
            $('.add-hod-attachment-file').trigger('click');
        });

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
