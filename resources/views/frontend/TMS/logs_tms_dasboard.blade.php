@extends('frontend.layout.main')
@section('container')
@if (Helpers::checkRoles(6))
@include('frontend.TMS.head')
@endif

@php
$divisions = DB::table('q_m_s_divisions')->select('id', 'name')->get();
@endphp
<style>
   
    .cctabcontent {
        padding: 20px;
        border: 1px solid #ccc;
        border-top: none;
    }
    .tmstablelast td {
            min-height: 60px; 
            padding: 14px 5px; 
        }

        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a,
        .pagination li span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination li.active span {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .pagination li.disabled span {
            color: #ccc;
        }
</style>

    
    <script>
        $(document).ready(function() {
            $('#search').on('change', function() {
                var selectedTrainee = $(this).val();
                if (selectedTrainee) {
                    $('.training-row').hide();
                    $('.training-row[data-trainee="' + selectedTrainee + '"]').show();
                } else {
                    $('.training-row').show();
                }
            });
        });
    </script>
    
    <div id="tms-dashboard">
        <div class="form-field-head">
            <div class="pr-id">
                Trainees Logs


            </div>

           
        </div>
        
    
    <div>
        <div class="inner-block tms-block cctabcontent" style="margin-top:50px; display:block;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center;">
                        <label for="status" style="margin-left: 20px;"><b>Trainee :</b></label>
                        <select name="" id="search" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; width: 200px;">
                            <option value="">Select All</option>
                            @php
                                $uniqueTrainings = collect($paginatedResults->items())->unique('traning_plan_name');
                            @endphp
                            @foreach($uniqueTrainings as $training)
                                <option value="{{ $training['traning_plan_name'] }}">{{ $training['traning_plan_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <a class="text-white" href="{{ url('TMS') }}"><button type="button" class="exit" style="padding: 5px; border-radius: 4px;">Back</button></a>
                    </div>
                </div><br>
                <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width:5%">Row</th>
                            <th>Trainee Name</th>
                            <th>Trainee Plan Id</th>
                            <th>Due Date</th>
                            <th>Attendance</th>
                            <th>Pass/Fail</th>
                            <th>Remark</th>
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paginatedResults as $index => $training)
                            <tr class="training-row" data-trainee="{{ $training['traning_plan_name'] }}">
                                <td>{{ $paginatedResults->firstItem() + $index }}</td>
                                <td>{{ $training['traning_plan_name'] }}</td>
                                <td>{{ $training['trainee'] }}-TP</td>
                                <td>{{ $training['due_date'] }}</td>
                                @php
                                    $trainingstatus = DB::table('training_statuses')->where(['user_id' => $training['trainee'], 'training_id' => $training['id']])->latest()->first();
                                @endphp
                                <td>{{ $trainingstatus ? 'Yes' : ($training['due_date'] < now() ? 'No' : 'Pending') }}</td>
                                <td>{{ $trainingstatus ? 'Pass' : 'Fail' }}</td>
                                <td></td>
                                <td> <i class="fa-solid fa-file-pdf"></i></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $paginatedResults->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
   
@endsection
