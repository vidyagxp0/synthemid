@php
    $printRequest = DB::table('print_requests')->get();
    // dd($printRequest);
@endphp

<div class="main-head">
    <div>Print Request</div>
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
                Initiated By
            </th>
            <th class="short-desc">
                Short Description
            </th>
            <th class="create-date">
                Print Request for
            </th>
            <th class="modify-date">
                Created Date
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
                <td class="pr-id" style="text-decoration:underline"><a href="{{ url('print-request/edit', $doc->id) }}">
                        000{{ $doc->id }}
                    </a>
                </td>
                <td class="division">
                    {{ Helpers::getDivisionName($doc->division_id) }}
                </td>
                <td class="division">
                    {{ Helpers::getInitiatorName($doc->originator_id) }}
                </td>

                <td style="display: inline-block;
                width: 305px;
                white-space: nowrap;
                overflow: hidden !important;
                text-overflow: ellipsis" class="short-desc">
                    {{ $doc->short_desc }}
                </td>
                <td class="create-date">
                    {{ Helpers::getInitiatorName($doc->permission_user_id) }}
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
                        <div class="action-down-btn">Action <i class="fa-solid fa-angle-down"></i></div>
                        <div class="action-block">
                            <a href="{{ url('print-request/edit/', $doc->id) }}">Edit
                            </a>
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