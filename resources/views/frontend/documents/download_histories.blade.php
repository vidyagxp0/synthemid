@extends('frontend.layout.main')
@section('container')

<div id="audit-trial">
    <div class="container-fluid">
        <div class="audit-trial-container">

            <div class="inner-block">
                <div class="main-head">

                    <div class="btn-group">
                        <!-- <button class="button_theme1" onclick="window.print();return false;" type="button">Print</button> -->
                    </div>
                </div>

                <div class="activity-table">
                    <table class="table table-bordered" id='auditTable'>
                        <thead>
                            <tr>
                                <th>Document ID</th>
                                <th>User ID</th>
                                <th>Copy Issued</th>
                                {{-- <th>Print Reason</th> --}}
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($data))
                            @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->document_id }}</td>
                                <td>{{ Helpers::getInitiatorName($item->user_id) }}</td>
                                <td>{{ $item->issue_copies }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-M-Y H:i A') }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td>Data Not Found!</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection