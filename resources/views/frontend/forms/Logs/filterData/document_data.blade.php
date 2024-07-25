@forelse ($document as $documents)
    <tr>
        <td>{{$loop->index+1}}</td>
        <td>{{ $documents->document_name }}</td>
        <td>{{ $documents->document_type_id }}</td>
        <td>{{ $documents->department_id }}</td>
        <td>{{ Helpers::getInitiatorName($documents->originator_id) }}</td>
        <td>{{ Helpers::getdateFormat($documents->due_dateDoc) }}</td>
        <td>{{ Helpers::getdateFormat($documents->effective_date) }}</td>
        <!-- <td>{{ Helpers::getdateFormat($documents->effective_date) }}</td> -->
        @if(!empty($documents->cc_reference_record) && is_array($documents->cc_reference_record))
            @foreach ($documents->cc_reference_record as $new)
                @php
                    $ccfind = CC::find($new);
                    // Format each record ID
                    $formattedRecords = [];
                    foreach ($ccfind as $id) {
                            $formattedRecords[] = Helpers::getDivisionName($id->division_id) . '/CC/' . date('Y') . '/' . Helpers::recordFormat($id->record);
                        }

                        // Implode the formatted records into a single string
                        $ccmodifyrecord = implode(', ', $formattedRecords);
                @endphp
                <td>{{ $ccmodifyrecord }}</td>
            @endforeach
        @endif
        <td>{{ $documents->status }}</td>        
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


