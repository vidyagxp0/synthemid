@extends('frontend.layout.main')
@section('container')
    {{-- ======================================
                    DASHBOARD
    ======================================= --}}
    <div id="document">
        <div class="container-fluid">
            <div class="dashboard-container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="document-left-block">
                            <div class="inner-block create-block">
                                <div class="head text-right mb-0">
                                    <a href="{{ route('rcms.logs.show', 'pending-approver') }}" target="_blank">
                                        <i class="fa-solid fa-log"></i>Pending Approver Log
                                    </a>
                                    <a href="{{ route('rcms.logs.show', 'document') }}" target="_blank">
                                        <i class="fa-solid fa-log"></i>Log
                                    </a>
                                    <a href="#" id="set-division">
                                        <i class="fa-solid fa-plus"></i> Create
                                    </a>
                                    <a href="#" id="">
                                        <button data-bs-toggle="modal" data-bs-target="#pdf-modal">
                                            Print Document
                                        </button>
                                    </a>
                                    {{-- <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Import Data
                                    </button> --}}
                                    {{-- <a href="{{route('documentExportEXCEL')}}">
                                Export EXCEL
                                </a> --}}
                                </div>

                            </div>
                            <div class="inner-block table-block">
                                <div style="display:flex; justify-content:space-around;" class=" main-filter">

                                    <div class="filter-block">
                                        <div class="drop-filter-block">
                                            <div class="icon">
                                                <i class="fa-solid fa-gauge-high"></i>
                                            </div>
                                            <div class="right">
                                                <label for="status">Status</label>
                                                <select name="status" class="filterSelect">
                                                    <option value="">All</option>
                                                    @php
                                                        $uniqueStatus = $documentStatus->pluck('status')->unique();
                                                    @endphp
                                                    @foreach ($uniqueStatus as $status)
                                                        <option value="{{ $status }}">{{ $status }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        {{-- <div class="search-bar">
                                            <input id="searchInput" type="text" name="search"
                                                placeholder="Search from the list...">
                                            <label for="search"><i class="fa-solid fa-magnifying-glass"></i></label>
                                        </div> --}}
                                    </div>

                                    <div class="filter-block">
                                        <div class="drop-filter-block">
                                            <div class="icon">
                                                <i class="fa-solid fa-file"></i>
                                            </div>
                                            <div class="right">
                                                <label for="document_type_id">Document Type</label>
                                                <select name="document_type_id" class="filterSelect">
                                                    <option value="">All</option>
                                                    @foreach ($documentTypes as $data)
                                                        <option value="{{ $data->id }}">{{ $data->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="filter-block">
                                        <div class="drop-filter-block">
                                            <div class="icon">
                                                <i class="fa-solid fa-sitemap"></i>
                                            </div>
                                            <div class="right">
                                                <label for="division_id">Division</label>
                                                <select name="division_id" class="filterSelect">
                                                    <option value="">All</option>
                                                    @foreach ($divisions as $data)
                                                        <option value="{{ $data->id }}">{{ $data->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="filter-block">
                                        <div class="drop-filter-block">
                                            <div class="icon">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div class="right">
                                                <label for="originator_id">Originator</label>
                                                <select name="originator_id" class="filterSelect">
                                                    <option value="">All</option>
                                                    @foreach ($originator as $data)
                                                        <option value="{{ $data->id }}">{{ $data->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="loadingRecords">
                                    <p>Fetching records...</p>
                                </div>
                                <div class="record-body">
                                    @include('frontend.documents.comps.record_table')
                                </div>

                                @php
                                    $printRequest = DB::table('print_requests')->get();
                                    // dd($printRequest);
                                @endphp

                                <div class="main-head" style="grid-template-columns: none !important;">
                                    <div style="text-align: left;">Print Request</div>
                                </div>
                                <div class="table-list">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th class="pr-id" data-bs-toggle="modal" data-bs-target="#division-modal">
                                                ID
                                            </th>
                                            <th class="division">
                                                Division
                                            </th>
                                            <th class="division">
                                                Requested By
                                            </th>
                                            <th class="short-desc">
                                                Request for
                                            </th>
                                            <th class="short-desc">
                                                Reason for Print
                                            </th>
                                            <th class="modify-date">
                                                Requested Date
                                            </th>
                                            <th class="modify-date">
                                                Due Date
                                            </th>
                                            <th class="status">
                                                Status
                                            </th>
                                            <th class="action">
                                                Action
                                            </th>
                                        </thead>
                                        <tbody id="searchTable">
                                            @if (count($printRequest) > 0)
                                                @foreach ($printRequest as $doc)
                                                    <tr>
                                                        <td class="pr-id" style="text-decoration:underline"><a
                                                                href="{{ url('print-request/edit', $doc->id) }}">
                                                                000{{ $doc->id }}
                                                            </a>
                                                        </td>
                                                        <td class="division">
                                                            {{ Helpers::getDivisionName($doc->division_id) }}
                                                        </td>
                                                        <td class="division">
                                                            {{ Helpers::getInitiatorName($doc->originator_id) }}
                                                        </td>
                                                        <td class="assign-name">
                                                            {{ $doc->request_for }}
                                                        </td>
                                                        <td style="
                                                            width: 305px;
                                                            white-space: nowrap;
                                                            overflow: hidden !important;
                                                            text-overflow: ellipsis"
                                                            class="short-desc">
                                                            {{ $doc->print_reason }}
                                                        </td>
                                                        <td class="assign-name">
                                                            {{ $doc->created_at }}
                                                        </td>
                                                        <td class="modify-date">
                                                            {{ $doc->due_date }}
                                                        </td>
                                                        <td class="status">
                                                            {{ $doc->status }}
                                                        </td>
                                                        <td class="action">
                                                            <div class="action-dropdown">
                                                                <div class="action-down-btn">Action <i
                                                                        class="fa-solid fa-angle-down"></i></div>
                                                                <div class="action-block">
                                                                    <a href="{{ url('print-request/edit', $doc->id) }}">Edit
                                                                    </a>
                                                                    <a href="{{ url('print-request/single-report', $doc->id) }}">Single Report
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                            @endif

                                        </tbody>
                                    </table>
                                    @if (isset($count))
                                        {!! $documents->links() !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-xl-3 col-lg-3">
                        <div class="document-right-block">
                            <div class="inner-block recent-record">
                                <div class="head">
                                    Recent Records
                                </div>
                                <div class="record-list">
                                    <div>
                                        <div class="icon">
                                            <i class="fa-solid fa-gauge-high"></i>
                                        </div>
                                        <div><a href="#">DMS/TMS Dashboard</a></div>
                                    </div>
                                    <div>
                                        <div class="icon">
                                            <i class="fa-solid fa-gauge-high"></i>
                                        </div>
                                        <div><a href="#">Amit Guru</a></div>
                                    </div>
                                    <div>
                                        <div class="icon">
                                            <i class="fa-solid fa-gauge-high"></i>
                                        </div>
                                        <div><a href="#">Change Control Dashboard</a></div>
                                    </div>
                                    <div class="mb-0">
                                        <div class="icon">
                                            <i class="fa-solid fa-gauge-high"></i>
                                        </div>
                                        <div><a href="#">EQMS Home Dashboard</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="inner-block recent-items">
                                <div class="head">
                                    Recent Items (0)
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Print Document</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('document.print.pdf.history') }}" method="POST" target="_blank">
                    @csrf
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="group-input mb-3">
                            <label for="document-name" class="form-label">Document Name</label>
                            <input type="text" id="document_name" class="form-control" disabled>
                            <input type="hidden" id="hidden-document-name" name="document_name">
                        </div>
                        
                        <div class="group-input mb-3">
                            <label for="issue_copies">No. Of Copies <span class="text-danger">*</span></label>
                            <input type="number" name="issue_copies" value="1" min="1" class="form-control w-100" required>
                        </div>
                        <div class="group-input mb-3">
                            <label for="print_reason">Print Reason <span class="text-danger">*</span></label>
                            <textarea name="print_reason" class="form-control w-100" maxlength="255" required></textarea>
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


    <div class="modal fade" id="pdf-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Documents</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body mt-3">
                    <div class="group-input">
                        <a data-doc-name="Analysis Protocol Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Analysis Protocol Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="BPR Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">BPR Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="CC Observations" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">CC Observations</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="ECR Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">ECR Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="Format Template 4" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Format Template 4</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="Format Template 3" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Format Template 3</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="Grid Backup CC" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Grid Backup CC</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="Process Flow Chart Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Process Flow Chart Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="SDS Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">SDS Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="SOP Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">SOP Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="Specification TP Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Specification TP Template</a>
                    </div>
                    <div class="group-input mt-3">
                        <a data-doc-name="Specification Template" href="#" data-bs-toggle="modal" data-bs-target="#print-modal">Specification Template</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="import" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="group-input mb-3">
                            <label for="file">Upload Document (All PDF with csv or xls)</label>
                            <input type="file" name="files[]" multiple>
                        </div>
                        <div class="download mb-3">
                            <div class="title">(Download the example format from here.)</div>
                            <a href="{{ asset('user/images/document.xls') }}" class="btn btn-primary" download>
                                Download
                            </a>
                        </div>
                        <div>
                            <button type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="division-modal" class="d-none">
        <div class="division-container">
            <div class="content-container">
                <form action="{{ route('division_submit') }}" method="post">
                    @csrf
                    <div class="division-tabs">
                        <div class="tab">
                            @php
                                // Get the user's roles
$userRoles = DB::table('user_roles')
    ->where('user_id', Auth::user()->id)
    ->get();
// Initialize an empty array to store division IDs
$divisionIds = [];
// Loop through user's roles
                                foreach ($userRoles as $role) {
                                    // Store division IDs from user's roles
    $divisionIds[] = $role->q_m_s_divisions_id;
}
// Retrieve divisions where status = 1 and the division ID is in the array of division IDs
$divisions = DB::table('q_m_s_divisions')
    ->where('status', 1)
    ->whereIn('id', $divisionIds)
                                    ->get();
                            @endphp
                            <style>
                                #division-modal .tab a.active {
                                    background-color: #23a723 !important;
                                    color: white !important;
                                }
                            </style>
                            @foreach ($divisions as $temp)
                                <input type="hidden" value="{{ $temp->id }}" name="division_id" required>
                                <a style="display: block;
                                    background-color: inherit;
                                    color: black;
                                    padding: 5px 10px;
                                    width: 100%;
                                    border: none;
                                    outline: none;
                                    text-align: left;
                                    cursor: pointer;
                                    transition: 0.3s;"
                                    class="divisionlinks"
                                    onclick="openDivision(event, {{ $temp->id }})">{{ $temp->name }}</a>
                            @endforeach
                        </div>
                        @php
                            $process = DB::table('processes')->get();
                        @endphp
                        @foreach ($process as $temp)
                            <div id="{{ $temp->division_id }}" class="divisioncontent">
                                @php
                                    $pro = DB::table('processes')
                                        ->where('division_id', $temp->division_id)
                                        ->get();
                                @endphp
                                @foreach ($pro as $test)
                                    <label for="process">
                                        <input type="radio" class="process_id_reset" for="process"
                                            value="{{ $test->id }}" name="process_id" required>
                                        {{ $test->process_name }}
                                    </label>
                                    <label for="process">
                                        <input type="radio" class="process_id_reset" for="process"
                                            value="print-request" name="process_id" required> Print / Download Request
                                    </label>
                                @endforeach
                            </div>
                        @endforeach

                    </div>
                    <div class="button-container">

                        <a href="/documents"
                            style="border: 1px solid grey;
                        letter-spacing: 1px;
                        font-size: 0.9rem;
                        padding: 3px 10px;
                        background: black;
                        color: white;">Cancel</a>
                        <button id="submit-division" type="submit">Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.8/axios.min.js"
        integrity="sha512-PJa3oQSLWRB7wHZ7GQ/g+qyv6r4mbuhmiDb8BjSFZ8NZ2a42oTtAq5n0ucWAwcQDlikAtkub+tPVCw4np27WCg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            let postUrl = "{{ route('record.filter') }}";
            $('.loadingRecords').hide();
            async function updateRecords() {
                $('.loadingRecords').show();
                let data = {
                    status: $('select[name=status]').val(),
                    document_type_id: $('select[name=document_type_id]').val(),
                    division_id: $('select[name=division_id]').val(),
                    originator_id: $('select[name=originator_id]').val(),
                }

                const res = await axios.post(postUrl, data)
                $('.record-body').html(res.data.html);
                $('.loadingRecords').hide();
            }

            $('.filterSelect').change(function() {
                try {
                    updateRecords()
                } catch (err) {
                    console.log("Error", err.message);
                }
            })
        })
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Get all the links in the pdf-modal
            var links = document.querySelectorAll('#pdf-modal .group-input a');

            links.forEach(function(link) {
                link.addEventListener('click', function() {
                    // Get the document name from the data attribute
                    var docName = this.getAttribute('data-doc-name');

                    // Set the value in the hidden input field in the print-modal
                    document.getElementById('document_name').value = docName;
                    document.getElementById('hidden-document-name').value = docName;
                });
            });
        });
    </script>

@endsection
