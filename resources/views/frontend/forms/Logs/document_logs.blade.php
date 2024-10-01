@extends('frontend.rcms.layout.main_rcms')
@section('rcms_container')
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        function openTab(tabName, ele) {
            let buttons = document.querySelector('.process-groups').children;
            let tables = document.querySelector('.process-tables-list').children;
            for (let element of Array.from(buttons)) {
                element.classList.remove('active');
            }
            ele.classList.add('active');
            for (let element of Array.from(tables)) {
                element.classList.remove('active');
                if (element.getAttribute('id') === tabName) {
                    element.classList.add('active');
                }
            }
        }
    </script>

    <style>
        header .header_rcms_bottom {
            display: none;
        }

        .filter-sub {
            display: flex;
            gap: 16px;
            margin-left: 13px
        }

        .filter-bar {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-item {
            display: flex;
            flex-direction: row;
            margin-right: 20px;
            gap: 10px;
        }

        .table-responsive {
            height: 100vh;
            overflow-x: auto;
        }

        .filter-item label {
            margin-bottom: 5px;
        }

        table {
            overflow: auto;
        }
    </style>
    <div id="rcms-desktop">
        <div class="process-groups">
            <div class="active" onclick="openTab('internal-audit', this)">Document Log</div>
        </div>
        <div class="main-content">
            <div class="container-fluid">
                <div class="process-tables-list">
                    <div class="process-table active" id="internal-audit">
                        <div class="mt-1 mb-2 bg-white" style="height: 65px">
                            <div class="d-flex align-items-center">
                                <div class="scope-bar ml-3">
                                    <button style="width: 70px; margin-left:5px" class="print-btn btn btn-primary">Print</button>
                                </div>
                                <div class="flex-grow-2" style="margin-left:-50px; margin-bottom:12px">
                                    <div class="filter-bar">
                                        <!-- <div class="filter-item">
                                            <label for="initiator_group">Department</label>
                                            <select name="Initiator_Group" id="initiator_group" class="form-control">
                                                <option value="">Enter Your Selection Here</option>
                                                <option value="CQA">Corporate Quality Assurance</option>
                                                <option value="QAB">Quality Assurance Biopharma</option>
                                                <option value="CQC">Central Quality Control</option>
                                                <option value="MANU">Manufacturing</option>
                                                <option value="PSG">Plasma Sourcing Group</option>
                                                <option value="CS">Central Stores</option>
                                                <option value="ITG">Information Technology Group</option>
                                                <option value="MM">Molecular Medicine</option>
                                                <option value="CL">Central Laboratory</option>
                                                <option value="TT">Tech team</option>
                                                <option value="QA">Quality Assurance</option>
                                                <option value="QM">Quality Management</option>
                                                <option value="IA">IT Administration</option>
                                                <option value="ACC">Accounting</option>
                                                <option value="LOG">Logistics</option>
                                                <option value="SM">Senior Management</option>
                                                <option value="BA">Business Administration</option>
                                            </select>
                                        </div> -->
                                        <div class="filter-item">
                                            <label for="division_id">Division</label>
                                            <select class="custom-select" id="division_id">
                                                <option value="">Select Option</option>
                                                <option value="1">Corporate</option>
                                                <option value="2">Plant</option>
                                            </select>
                                        </div>
                                        <div class="filter-item">
                                            <label for="date_from_document">Date From</label>
                                            <input type="date" class="custom-select" id="date_from_document">
                                        </div>
                                        <div class="filter-item">
                                            <label for="date_to_document">Date To</label>
                                            <input type="date" class="custom-select" id="date_to_document">
                                        </div>
                                        <!-- <div class="filter-item">
                                            <label for="deviationRelate">Deviation Related to</label>
                                            <select class="custom-select" id="deviationRelate">
                                                <option value="">Select Option</option>
                                                <option value="Facility">Facility</option>
                                                <option value="Equipment/Instrument">Instrument</option>
                                                <option value="Documentationerror">Documentation error</option>
                                                <option value="STP/ADS_instruction">STP/ADS instruction</option>
                                                <option value="Packaging&Labelling">Packaging & Labelling</option>
                                                <option value="Material_System">Material System</option>
                                                <option value="Laboratory_Instrument/System">Laboratory Instrument/System</option>
                                                <option value="Utility_System">Utility System</option>
                                                <option value="Computer_System">Computer System</option>
                                                <option value="Document">Document</option>
                                                <option value="Data integrity">Data integrity</option>
                                                <option value="SOP Instruction">SOP Instruction</option>
                                                <option value="BMR/ECR Instruction">BMR/ECR Instruction</option>
                                                <option value="Water System">Water System</option>
                                                <option value="Anyother(specify)">Any other (specify)</option>
                                            </select>
                                        </div> -->
                                        <div class="filter-item">
                                            <label for="datewise_document">Select Period</label>
                                            <select class="custom-select" id="datewise_document">
                                                <option value="">Select</option>
                                                <option value="Yearly">Yearly</option>
                                                <option value="Quarterly">Quarterly</option>
                                                <option value="Monthly">Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-block">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">Sr.No.</th>
                                            <th>Document Name</th>
                                            <th>Document Type</th>
                                            <th>Department</th>
                                            <th>Author</th>
                                            <th>Due Date</th>
                                            <th>Effective Date</th>
                                            <!-- <th>Approval Date</th> -->
                                            <th>CC References</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableData">
                                        @include('frontend.forms.Logs.filterData.document_data')
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center" style="margin-top: 10px;">
                                    <div class="spinner-border text-primary" role="status" id="spinner">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.2/axios.min.js" integrity="sha512-JSCFHhKDilTRRXe9ak/FJ28dcpOJxzQaCd3Xg8MyF6XFjODhy/YMCM8HW0TFDckNHWUewW+kfvhin43hKtJxAw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('#spinner').hide();

        const filterData = {
            // departmentDeviation: null,
            division_idDocument: null,
            date_fromDocument: null,
            date_toDocument: null,
            // audit_type: null,
            period: null
        };

        // $('#initiator_group').change(function() {
        //     filterData.departmentDeviation = $(this).val();
        //     filterRecords();
        // });

        $('#division_id').change(function() {
            filterData.division_idDocument = $(this).val();
            filterRecords();
        });

        // $('#deviationRelate').change(function() {
        //     filterData.audit_type = $(this).val();
        //     filterRecords();
        // });

        $('#date_from_document').change(function() {
            filterData.date_fromDocument = $(this).val();
            filterRecords();
        });

        $('#date_to_document').change(function() {
            filterData.date_toDocument = $(this).val();
            filterRecords();
        });

        $('#datewise_document').change(function() {
            filterData.period = $(this).val();
            filterRecords();
        });

        async function filterRecords() {
            $('#tableData').html('');
            $('#spinner').show();

            try {
                const postUrl = "{{ route('api.document.filter') }}";
                const res = await axios.post(postUrl, filterData);

                if (res.data.status === 'ok') {
                    $('#tableData').html(res.data.body);
                }
            } catch (err) {
                console.log('Error in filterRecords', err.message);
            }

            $('#spinner').hide();
        }
    </script>
    
@endsection
