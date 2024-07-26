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
            Print / Download Request
        </div>
        @if(isset($_GET['id']))
        <div class="division-bar">
            <strong>Site Division/Project</strong> :
            {{ Helpers::getDivisionName($_GET['id'])}} / Print / Download Request 
            {{-- {{ $division->dname }} / {{ $division->pname }} --}}
        </div>
        @endif
    </div>

    <div id="data-fields">

        <div class="container-fluid">
            <div class="tab">
                <button class="tablinks active" onclick="openData(event, 'doc-info')" id="defaultOpen">General information</button> 
                <button class="tablinks" onclick="openData(event, 'hodcft')">HOD Input</button>
                <button class="tablinks" onclick="openData(event, 'qa')">QA Input</button>
                {{-- <button class="tablinks" onclick="openData(event, 'sign')">Signature</button> --}}
            </div>
    

            <form id="document-form" action="{{ route('print-request.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div id="step-form">
                    <!-- Tab content -->
                    <div id="doc-info" class="tabcontent">

                        <div class="input-fields">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="originator">Requested By</label>
                                        <div class="default-name">{{ Auth::user()->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="group-input">
                                        <label for="open-date">Date Opened</label>
                                        <div class="default-name"> {{ date('d-M-Y') }}</div>
                                    </div>
                                </div>                                           

                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-type">Sile/Location <span class="text-danger">*</span></label>
                                        <select name="division_id" required>
                                            <option value="" selected>Enter your Selection</option>
                                            <option value="1" >Corporate</option>
                                            <option value="2" >Plant</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="doc-type">Request For <span class="text-danger">*</span></label>
                                        <select name="request_for" required>
                                            <option value="Print" selected>Print</option>
                                            <option value="Download" >Download</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="short-desc">Short Description<span class="text-danger">*</span></label>
                                        <span id="new-rchars">255</span>
                                        characters remaining
                                        <input type="text" required id="short_desc" name="short_description" maxlength="255">
                                    </div>
                                    {{-- <p id="short_descError" style="color:red">**Short description is required</p> --}}

                                </div>

                                <div class="col-lg-6">
                                    <div class="group-input">
                                        <label for="reference_records">Reference Records</label>
                                        <select id="choices-multiple-remove-button" class="choices-multiple-reviewer"
                                            name="reference_records[]" placeholder="Select Reference Records" multiple >
                                            @foreach ($documentList as $document)
                                                <option value="{{ $document->id }}">
                                                    {{ Helpers::getDivisionName($document->division_id) }}/Document/{{ date('Y') }}/{{ Helpers::recordFormat($document->record)}}/{{ $document->document_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 new-date-data-field">
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
                                <div class="col-lg-6 mb-3">
                                    <div class="group-input">
                                        <label for="No. of Copies">No. of Copies <span class="text-danger">*</span></label>
                                        <input  required type="number" min="0" name="no_of_copies" id="no_of_copies">
        
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="group-input">
                                        <label for="WaterMark">WaterMark</label>
                                        <input  type="file" name="water_mark_attachment" id="water_mark_attachment">
        
                                    </div>
                                </div>
                               
                                
                                <div class="col-md-12">
                                    <div class="group-input">
                                        <label for="short-desc">Reason for Print <span class="text-danger">*</span></label>
                                        <textarea id="print_reason" name="print_reason"></textarea>
                                    </div>
                                    {{-- <p id="short_descError" style="color:red">**Short description is required</p> --}}
                                </div>
                            
                        </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="hods">HOD<span class="text-danger">*</span></label>
                                        <select name="hods" id="doc-type" required>
                                            <option value="" selected>Select HODs</option>
                                            @foreach ($hods as $me)
                                                <option value="{{ $me->id }}">{{ $me->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="group-input">
                                        <label for="hods">QA<span class="text-danger">*</span></label>
                                        <select name="qa" id="doc-type" required>
                                            <option value="" selected>Select QA</option>
                                            @foreach ($qa as $me)
                                                <option value="{{ $me->id }}">{{ $me->name }}</option>
                                            @endforeach
                                        </select>
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
                            <button type="submit" value="save" name="submit" class="saveButton">Save</button>
                            <button type="button" class="nextButton" id="DocnextButton">Next</button>
                            <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white"> Exit </a>
                            </button> 
                        </div>
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
                                        <label for="comments">HOD Remarks</label>
                                        <textarea disabled name="hod_remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="group-input">
                                        <label for="Audit Attachments">HOD Attachments</label>
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
                                        <label for="Warehousefeedback">HOD Completed By</label>
                                        <input readonly type="text" name="hod_by" id="hod_by">
        
                                    </div>
                                </div>
        
                                <div class="col-lg-6 new-date-data-field warehouse">
                                    <div class="group-input input-date">
                                        <label for="HOD Completed On">HOD Completed On</label>
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
                    <div id="sign" class="tabcontent">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Originated By 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="review-names">
                                    <div class="orig-head">
                                        Originated On 
                                    </div>
                                </div>
                            </div>
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
