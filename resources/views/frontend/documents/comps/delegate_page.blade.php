@extends('frontend.layout.main')
@section('container')

<style>
    .container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .group-input label {
        font-weight: bold;
    }

    .group-input {
        margin-bottom: 20px;
    }

    .choices-multiple-approver, .choices-multiple-reviewer {
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px;
    }

    .saveButton, .backButton {
        width: 150px;
        height: 40px;
        border: none;
        border-radius: 4px;
        margin: 10px 5px;
    }

    .saveButton {
        background-color: #4274da;
        color: white;
    }

    .backButton {
        background-color: #6c757d;
        color: white;
    }

    .saveButton:hover {
        background-color: #4274da;
        color: white;
    }

    .backButton:hover {
        color: white;
        background-color: #5a6268;
    }

    .button-block {
        text-align: center;
        margin-top: 20px;
    }

    h5 {
        margin-bottom: 30px;
    }
</style>

<div class="container mt-5">
    <h5 class="text-primary text-center">Here you can modified the Authors, HODs, QAs, Reviewers, and Approvals Persons</h5>
    <form action="{{url('delegate-updated')}}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-6">
                <div class="group-input">
                    <label for="drafter">Authors 
                        @if($document->stage > 2) 
                            <span style="color: red; font-weight: bold; font-size: 13px;">(Activity Already performed)</span> 
                        @endif
                    </label>
                    <select id="choices-multiple-remove-button" class="choices-multiple-approver form-control" name="drafters[]" multiple {{$document->stage > 2 ? 'disabled' : ''}}>
                        @if (!empty($drafter))
                            @foreach ($drafter as $lan)
                                @if(Helpers::checkUserRolesDrafter($lan))
                                    <option value="{{ $lan->id }}" @if(in_array($lan->id, explode(',', $document->drafters))) selected @endif>
                                        {{ $lan->name }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="group-input">
                    <label for="hods">HOD's
                        @if($document->stage > 3) 
                            <span style="color: red; font-weight: bold; font-size: 13px;">(Activity Already performed)</span> 
                        @endif
                    </label>
                    <select id="choices-multiple-remove-button" class="choices-multiple-approver form-control" name="hods[]" multiple {{$document->stage > 3 ? 'disabled' : ''}}>
                        @foreach ($hods as $hod)
                            <option value="{{ $hod->id }}" @if(in_array($hod->id, explode(',', $document->hods))) selected @endif>
                                {{ $hod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="group-input">
                    <label for="qa">QA's
                        @if($document->stage > 4) 
                            <span style="color: red; font-weight: bold; font-size: 13px;">(Activity Already performed)</span> 
                        @endif
                    </label>
                    <select id="choices-multiple-remove-button" class="choices-multiple-approver form-control" name="qa[]" multiple {{$document->stage > 4 ? 'disabled' : ''}}>
                        @foreach ($qa as $qa_person)
                            <option value="{{ $qa_person->id }}" @if(in_array($qa_person->id, explode(',', $document->qa))) selected @endif>
                                {{ $qa_person->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="group-input">
                    <label for="reviewers">Reviewers
                        @if($document->stage > 5) 
                            <span style="color: red; font-weight: bold; font-size: 13px;">(Activity Already performed)</span> 
                        @endif
                    </label>
                    <select id="choices-multiple-remove-button" class="choices-multiple-reviewer form-control" name="reviewers[]" multiple {{$document->stage > 5 ? 'disabled' : ''}}>
                        @if (!empty($reviewer))
                            @foreach ($reviewer as $lan)
                                @if(Helpers::checkUserRolesreviewer($lan))
                                    <option value="{{ $lan->id }}" @if(in_array($lan->id, explode(',', $document->reviewers))) selected @endif>
                                        {{ $lan->name }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="group-input">
                    <label for="approvers">Approvers
                        @if($document->stage > 6) 
                            <span style="color: red; font-weight: bold; font-size: 13px;">(Activity Already performed)</span> 
                        @endif
                    </label>
                    <select id="choices-multiple-remove-button" class="choices-multiple-approver form-control" name="approvers[]" multiple {{$document->stage > 6 ? 'disabled' : ''}}>
                        @if (!empty($approvers))
                            @foreach ($approvers as $lan)
                                @if(Helpers::checkUserRolesApprovers($lan))
                                    <option value="{{ $lan->id }}" @if(in_array($lan->id, explode(',', $document->approvers))) selected @endif>
                                        {{ $lan->name }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <input type="hidden" name="document_id" value="{{$ids}}">
        <div class="button-block">
            @if($document->stage <= 6) 
            <button type="submit" name="submit" value="save" class="saveButton">Save</button>
            @endif
            <button type="button" class="backButton" onclick="previousStep()">Back</button>
        </div>
    </form>
</div>

@endsection
