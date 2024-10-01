    {{-- ======================================
                    TMS HEAD
    ======================================= --}}
    <div id="tms-head">
        <div class="head">Training Management System</div>
        <div class="link-list">
            {{-- <a style="cursor: pointer" onclick="

            window.open('/activity_log', '_blank', 'width=1200, height=900, top=0, left=0');"
                data-bs-toggle="tooltip" title="Training Log">
             Training Log
        </a> --}}

            <a href="{{ route('TMS.index') }}" class="tms-link">Dashboard</a>
            {{-- <a href="{{ route('') }}" class="tms-link">Logs</a> --}}
            <a data-bs-toggle="modal" data-bs-target="#logsid">Logs</a>
            <a href="{{ route('employee_new') }}" class="tms-link">Employee</a>
            <a href="{{ route('trainer_qualification') }}" class="tms-link">Trainer Qualification</a>
            <div class="tms-drop-block">
                <div class="drop-btn">Quizzes&nbsp;<i class="fa-solid fa-angle-down"></i></div>
                <div class="drop-list">
                    <a href="/question">Question</a>
                    <a href="/question-bank">Question Banks</a>
                    <a href="{{ route('quize.index') }}">Manage Quizzes</a>
                </div>
            </div>
            <div class="tms-drop-block">
                <div class="drop-btn">Activities&nbsp;<i class="fa-solid fa-angle-down"></i></div>
                <div class="drop-list">
                    <a href="{{ route('TMS.create') }}">Create Training Plan</a>
                    <a href="{{ url('TMS/show') }}">Manage Training Plan</a>
                    <a href="{{ url('induction_training') }}">Induction Training</a>
                    <a href="{{ url('job_training') }}">On The Job Training</a>

                </div>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    <div class="modal fade" id="logsid">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Logs</h4>
                </div>
                <div class="model-body">
                <form action="{{ route('logstms') }}" method="POST">
                    @csrf
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="group-input">
                            <label style="  display: flex;     gap: 18px;" for="capa-child">
                                <input type="radio" name="revision" value="traininglog">
                               Training Logs
                            </label>
                        </div><br>
                        <div class="group-input">
                            <label style=" display: flex;     gap: 16px;" for="root-item">
                                <input type="radio" name="revision" id="root-item"value="traineesLogs">
                              <span style="width: 100px;">Trainees Logs</span>
                            </label>
                        </div>
                       
                    </div>

                   
                    <div class="modal-footer">
                              <button type="submit">Submit</button>
                             <button type="button" data-bs-dismiss="modal">Close</button>                         
                   </div>
                </form>
                </div>
            </div>
        </div>
    </div>