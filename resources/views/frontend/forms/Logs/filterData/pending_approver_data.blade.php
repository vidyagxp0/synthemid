@forelse ($approverPending as $approverPendings)
    <tr>
        <td>{{$loop->index+1}}</td>
        <td>{{ $approverPendings->document_name }}</td>
        <td>{{ $approverPendings->document_type_id }}</td>
        <td>{{ $approverPendings->department_id }}</td>
        <td>{{ Helpers::getInitiatorName($approverPendings->originator_id) }}</td>
        <td>{{ Helpers::getdateFormat($approverPendings->due_dateDoc) }}</td>
        <td>{{ Helpers::getdateFormat($approverPendings->effective_date) }}</td>
        <td>{{ Helpers::getdateFormat($approverPendings->effective_date) }}</td>
        <td>{{ $approverPendings->cc_reference_record }}</td>
        <td>{{ $approverPendings->status }}</td>        
    </tr>
@empty 

    <tr>
        <td colspan="12" class="text-center">
        <div class="alert alert-warning my-2" style="--bs-alert-bg:#999793;     --bs-alert-color:#060606 ">
            Data Not Found
        </div>
        </td>
    </tr>  

@endforelse


