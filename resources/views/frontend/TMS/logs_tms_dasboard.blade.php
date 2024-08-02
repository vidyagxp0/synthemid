@extends('frontend.layout.main')
@section('container')
   
    <style>
        textarea.note-codable {
            display: none !important;
        }

        header {
            display: none;
        }
    .exit {
    margin-right: 20px;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#ObservationAdd').click(function(e) {
                function generateTableRow(serialNumber) {

                    var html =
                        '<tr>' +
                        '<td><input disabled type="text" name="jobResponsibilities[' + serialNumber +
                        '][serial]" value="' + serialNumber +
                        '"></td>' +
                        '<td><input type="text" name="jobResponsibilities[' + serialNumber +
                        '][job]"></td>' +
                        '<td><input type="text" class="Document_Remarks" name="jobResponsibilities[' +
                        serialNumber + '][remarks]"></td>' +


                        '</tr>';

                    return html;
                }

                var tableBody = $('#job-responsibilty-table tbody');
                var rowCount = tableBody.children('tr').length;
                var newRow = generateTableRow(rowCount + 1);
                tableBody.append(newRow);
            });
        });
    </script>
    <div>
    <div class="form-field-head">
        <div class="pr-id">
            Trainees Logs

            
        </div>

        <div>
                                            
                
                
        
        </div>
    </div>
    <div class="inner-block tms-block cctabcontent" style="margin-top:50px; display:block;"> 
 <div style="display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center;">
        <label for="status" style="margin-left: 20px;"><b>Trainee :</b></label>
        <input type="text" id="search" name="search" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; width: 200px;">
    
    </div>
    <div style="display: flex; align-items: center;">
    <button type="button" class="exit" style="padding: 5px; border-radius: 4px;"> <a class="text-white" href="{{ url('TMS') }}">
        Exit </a> </button>
    </div>
    </div>
    </div>
    <div class="mt-5">
        
        <table class="table table-bordered" style="width: 100%; border-collapse: collapse; border: 1px solid black">
            <thead>
                <tr style="background-color: #4274da;">
                    <th>Row</th>
                    <th>Trainee Name</th>
                    <th>Trainee Plan Id</th>
                    <th>Due Date</th>
                    <th>Att</th>
                    <th>Type</th>
                    <th>Due Date</th>
                    <th>Complains Date</th>
                </tr>
            </thead>
            <tbody class="tmstablelast">
                <tr>
                    <td>1</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



    {{-- ======================================
                    DATA FIELDS
    ======================================= --}}
  

    <script>
        function openCity(evt, cityName) {
            var i, cctabcontent, cctablinks;
            cctabcontent = document.getElementsByClassName("cctabcontent");
            for (i = 0; i < cctabcontent.length; i++) {
                cctabcontent[i].style.display = "none";
            }
            cctablinks = document.getElementsByClassName("cctablinks");
            for (i = 0; i < cctablinks.length; i++) {
                cctablinks[i].className = cctablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        const saveButtons = document.querySelectorAll('.saveButton1');
        const form = document.getElementById('step-form');
    </script>
    <script>
        VirtualSelect.init({
            ele: '#Facility, #Group, #Audit, #Auditee ,#reference_record, #designee, #hod'
        });
    </script>
@endsection
