<div class="main-head">
    <div>Records</div>
    <div>
        {{ count($documents) }} Results {{ isset($count) ? ' out of Results  ' .  $count : 'found' }}
    </div>
</div>
<div class="table-list">
    <table class="table table-bordered">
        <thead>
            <th class="pr-id" data-bs-toggle="modal" data-bs-target="#division-modal">
                ID
            </th>
            <th class="division">
                Document Type
            </th>
            <th class="division">
                Division
            </th>
            <th class="short-desc">
                Short Description
            </th>
            <th class="create-date">
                Create Date Time
            </th>
            <th class="assign-name">
                Originator
            </th>
            <th class="modify-date">
                Modify Date Time
            </th>
            <th class="status">
                Status
            </th>
            <th class="action">
                Action
            </th>
        </thead>
        <tbody id="searchTable">
            @if (count($documents) > 0)
            @foreach ($documents as $doc)
            {{-- {{dd($doc);}} --}}
            <tr>
                <td class="pr-id" style="text-decoration:underline"><a href="{{ route('documents.edit', $doc->id) }}">
                        000{{ $doc->id }}
                    </a>
                </td>
                <td class="division">
                    {{ Helpers::getFullDepartmentTypeName($doc->document_type_id) }}
                </td>
                <td class="division">
                    {{ Helpers::getDivisionName($doc->division_id) }}
                </td>

                <td style="
                width: 305px;
             
                overflow: hidden !important;
                text-overflow: ellipsis" class="short-desc">
                    {{ $doc->short_description }}
                </td>
                <td class="create-date">
                    {{ \Carbon\Carbon::parse($doc->created_at)->format('d-M-Y H:i A') }}
                </td>
                <td class="assign-name">
                    {{ Helpers::getInitiatorName($doc->originator_id) }}
                </td>
                <td class="modify-date">
                    {{ \Carbon\Carbon::parse($doc->updated_at)->format('d-M-Y H:i A') }}
                </td>
                <td class="status">
                    {{ Helpers::getDocStatusByStage($doc->stage, $doc->training_required) }}
                </td>
                <td class="action">
                    <div class="action-dropdown">
                        <div class="action-down-btn">Action <i class="fa-solid fa-angle-down"></i></div>
                        <div class="action-block">
                            <a href="{{ url('doc-details', $doc->id) }}">View
                            </a>

                            @if ($doc->status != 'Obsolete')
                                <a href="{{ route('documents.edit', $doc->id) }}">Edit</a>
                            @endif
                            @php
                                $userRoles = DB::table('users')->where(['id' => Auth::user()->id, 'delegate' => true])->first();
                                // $userRoleIds = $userRoles->pluck('q_m_s_roles_id')->toArray();
                                // dd($userRoles);
                            @endphp
                            @if ($userRoles)
                                {{-- <a href="javascript:void(0);" data-doc-id="{{ $doc->id }}" class="open-modal" data-toggle="modal" data-target="#delegateModal">Delegate</a> --}}
                                <a href="{{ url('delegate', $doc->id) }}">Delgate</a>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <center>
                <h5>Data not Found</h5>
            </center>
            @endif

        </tbody>
    </table>
    @if (isset($count))
    {!! $documents->links() !!}
    @endif
</div>
<div class="modal fade" id="delegateModal" tabindex="-1" role="dialog" aria-labelledby="delegateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delegateModalLabel">Delegate Settings</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{ url('delegate') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="document_id" id="document_id">
                <div>
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
  

<script>
    $(document).ready(function() {
        $('.open-modal').on('click', function() {
            var docId = $(this).data('doc-id');
            $('#delegateModal').modal('show');

            // Remove any previous click event attached to the button before adding a new one
            $('#confirm-delegate').off('click').on('click', function() {
                window.location.href = `/documents/delegate/${docId}`;
            });
        });
        
        // Optional: If you want to reset the modal or do something when it is closed
        $('#delegateModal').on('hidden.bs.modal', function() {
            // Reset or clean up actions
            $('#confirm-delegate').off('click'); // Remove click event to prevent memory leaks
        });
    });
    $(document).ready(function() {
        $('.open-modal').click(function() {
            var docId = $(this).data('doc-id'); // Get the doc ID from the data attribute
            $('#document_id').val(docId); // Set the value in the hidden input field
        });
    });
</script>