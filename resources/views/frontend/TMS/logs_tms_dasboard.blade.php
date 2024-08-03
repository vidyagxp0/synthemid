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
                            @foreach($processedTrainings as $training)
                                <option value="{{ $training['traning_plan_name'] }}">{{ $training['traning_plan_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <a class="text-white"
                                href="{{ url('TMS') }}"><button type="button" class="exit" style="padding: 5px; border-radius: 4px;"> 
                                Back  </button></a>
                    </div>
                </div><br>
                <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width:5%">Row</th>
                            <th>Trainee Name</th>
                            <th>Trainee Plan Id</th>
                            <th>Due Date</th>
                            <th>Attendance </th>
                            <th>Pass/Fail</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processedTrainings as $index => $training)
                        <tr class="training-row" data-trainee="{{ $training['traning_plan_name'] }}">
                            <td>{{ $index + 1 }}</td>
                                <td>{{ $training['traning_plan_name']}}</td>
                                <td>TP-{{ $training['trainee'] }}</td>
                                <td>{{ $training['due_date'] }}</td>
                            @php
                                $trainingstatus = DB::table('training_statuses')->where(['user_id'=>$training['trainee'],'training_id'=>$training['id']])->latest()->first();
                                // dd($trainingstatus);
                            @endphp
    
                                <td>{{ $trainingstatus ? 'Yes' : ($training['due_date'] < now() ? 'No' : 'Pending') }}</td>
                                <td>{{ $trainingstatus ? 'Pass' : '-'}}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
   
@endsection
