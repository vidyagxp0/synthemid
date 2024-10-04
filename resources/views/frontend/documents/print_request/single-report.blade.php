<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>VidyaGxP - Software</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .w-10 {
        width: 10%;
    }

    .w-20 {
        width: 20%;
    }

    .w-25 {
        width: 25%;
    }

    .w-30 {
        width: 30%;
    }

    .w-40 {
        width: 40%;
    }

    .w-50 {
        width: 50%;
    }

    .w-60 {
        width: 60%;
    }

    .w-70 {
        width: 70%;
    }

    .w-80 {
        width: 80%;
    }

    .w-90 {
        width: 90%;
    }

    .w-100 {
        width: 100%;
    }

    .h-100 {
        height: 100%;
    }

    header table,
    header th,
    header td,
    footer table,
    footer th,
    footer td,
    .border-table table,
    .border-table th,
    .border-table td {
        border: 1px solid black;
        border-collapse: collapse;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    table {
        width: 100%;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    footer .head,
    header .head {
        text-align: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    @page {
        size: A4;
        margin-top: 160px;
        margin-bottom: 60px;
    }

    header {
        position: fixed;
        top: -140px;
        left: 0;
        width: 100%;
        display: block;
    }

    footer {
        width: 100%;
        position: fixed;
        display: block;
        bottom: -40px;
        left: 0;
        font-size: 0.9rem;
    }

    footer td {
        text-align: center;
    }

    .inner-block {
        padding: 10px;
    }

    .inner-block tr {
        font-size: 0.8rem;
    }

    .inner-block .block {
        margin-bottom: 30px;
    }

    .inner-block .block-head {
        font-weight: bold;
        font-size: 1.1rem;
        padding-bottom: 5px;
        border-bottom: 2px solid #4274da;
        margin-bottom: 10px;
        color: #4274da;
    }

    .inner-block th,
    .inner-block td {
        vertical-align: baseline;
    }

    .table_bg {
        background: #4274da57;
    }
</style>

<body>

    <header>
        <table>
            <tr>
                <td class="w-70 head">
                    Print Request Single Report
                </td>
                <td class="w-30">
                    <div class="logo">
                        <img src="https://vidyagxp.com/vidyaGxp_logo.png" alt="" class="w-100">
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="w-30">
                    <strong>Print Request No.</strong>
                </td>
                <td class="w-40">
                    {{ Helpers::getDivisionName($data->division_id) }}/PR/{{ date('Y') }}/{{ $data->id ? str_pad($data->id, 4, '0', STR_PAD_LEFT) : '' }}
                </td>
                <td class="w-30">
                    <strong>Record No.</strong> {{ str_pad($data->id, 4, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        </table>
    </header>

    <div class="inner-block">
        <div class="content-table">
            <div class="block">
                <div class="block-head">
                    General Information
                </div>
                <table>
                    <tr>
                        <th class="w-20">Originator</th>
                        <td class="w-30">{{ $data->originator }}</td>

                        <th class="w-20">Date Opened</th>
                        <td class="w-30">{{ $data->initiated_on }}</td>
                    </tr>
                    <tr>
                        <th class="w-20">Due Date</th>
                        <td class="w-30">
                            @if ($data->due_date)
                            {{ Helpers::getdateFormat($data->due_date) }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Short Description</th>
                        <td class="w-80" colspan="3">
                            @if ($data->short_description)
                            {{ $data->short_description }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Related Records</th>
                        <td class="w-80" colspan="3">
                            @if(count($relatedRecords) > 0)
                            {{ implode(', ', $relatedRecords) }}
                            @else
                            No related records found.
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">HOD</th>
                        <td class="w-30">
                            @if ($data->hods)
                            {{ Helpers::getInitiatorName($data->hods) }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Initiated By</th>
                        <td class="w-30">
                            @if ($data->initiated_by)
                            {{ Helpers::getInitiatorName($data->initiated_by) }}
                            @else
                            Not Applicable
                            @endif
                        </td>

                        <th class="w-20">Initiated On</th>
                        <td class="w-30">
                            @if ($data->initiated_on)
                            {{ $data->initiated_on }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="border-table">
                <div class="block-head">
                    Initial Attachments
                </div>
                <table>
                    <tr class="table_bg">
                        <th class="w-20">S.N.</th>
                        <th class="w-60">Attachment</th>
                    </tr>
                    @if ($data->initial_attachments)
                    @foreach (json_decode($data->initial_attachments) as $key => $file)
                    <tr>
                        <td class="w-20">{{ $key + 1 }}</td>
                        <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                target="_blank"><b>{{ $file }}</b></a> </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="w-20">1</td>
                        <td class="w-20">Not Applicable</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- HOD Details -->

            <div class="block">
                <div class="block-head">
                    HOD Details
                </div>
                <table>
                    <tr>
                        <th class="w-20">HOD Remark</th>
                        <td class="w-80" colspan="3">
                            @if ($data->hod_remarks)
                            {{ $data->hod_remarks }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">HOD Completed By</th>
                        <td class="w-30">
                            @if ($data->hod_by)
                            {{ Helpers::getInitiatorName($data->hod_by) }}
                            @else
                            Not Applicable
                            @endif
                        </td>

                        <th class="w-20">HOD Completed On</th>
                        <td class="w-30">
                            @if ($data->hod_on)
                            {{ $data->hod_on }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="border-table">
                <div class="block-head">
                    HOD Attachments
                </div>
                <table>
                    <tr class="table_bg">
                        <th class="w-20">S.N.</th>
                        <th class="w-60">Attachment</th>
                    </tr>
                    @if ($data->hod_attachments)
                    @foreach (json_decode($data->hod_attachments) as $key => $file)
                    <tr>
                        <td class="w-20">{{ $key + 1 }}</td>
                        <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                target="_blank"><b>{{ $file }}</b></a> </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="w-20">1</td>
                        <td class="w-20">Not Applicable</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Supplier Details -->

            <div class="block">
                <div class="block-head">
                    QA Details
                </div>
                <table>
                    <tr>
                        <th class="w-20">QA Remarks</th>
                        <td class="w-80" colspan="3">
                            @if ($data->qa_remarks)
                            {{ $data->qa_remarks }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">QA Completed By</th>
                        <td class="w-30">
                            @if ($data->qa_by)
                            {{ Helpers::getInitiatorName($data->qa_by) }}
                            @else
                            Not Applicable
                            @endif
                        </td>

                        <th class="w-20">QA Completed By</th>
                        <td class="w-30">
                            @if ($data->qa_on)
                            {{ $data->qa_on }}
                            @else
                            Not Applicable
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="border-table">
                <div class="block-head">
                    QA Attachments
                </div>
                <table>
                    <tr class="table_bg">
                        <th class="w-20">S.N.</th>
                        <th class="w-60">Attachment</th>
                    </tr>
                    @if ($data->qa_attachments)
                    @foreach (json_decode($data->qa_attachments) as $key => $file)
                    <tr>
                        <td class="w-20">{{ $key + 1 }}</td>
                        <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                target="_blank"><b>{{ $file }}</b></a> </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="w-20">1</td>
                        <td class="w-20">Not Applicable</td>
                    </tr>
                    @endif
                </table>
            </div>

        </div>




        <!-- <div class="block">
            <div class="block-head">
                Activity Log
            </div>
            <table>
                <tr>
                    <th class="w-20">Submitted By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->submit_by }}</div>
                    </td>
                    <th class="w-20">Submitted On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->submit_on }}</div>
                    </td>
                    <th class="w-20">Submitted Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->submit_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Cancelled By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->cancelled_by }}</div>
                    </td>
                    <th class="w-20">Cancelled On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->cancelled_on }}</div>
                    </td>
                    <th class="w-20">Cancelled Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->cancelled_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Pending Qualification By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_qualification_by }}</div>
                    </td>
                    <th class="w-20">Pending Qualification On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_qualification_on }}</div>
                    </td>
                    <th class="w-20">Pending Qualification Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_qualification_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Pending Supplier By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_supplier_audit_by }}</div>
                    </td>
                    <th class="w-20">Pending Supplier On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_supplier_audit_on }}</div>
                    </td>
                    <th class="w-20">Pending Supplier Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_supplier_audit_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Pending Rejction By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_rejection_by }}</div>
                    </td>
                    <th class="w-20">Pending Rejction On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_rejection_on }}</div>
                    </td>
                    <th class="w-20">Pending Rejction Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->pending_rejection_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Supplier Approved By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->supplier_approved_by }}</div>
                    </td>
                    <th class="w-20">Supplier Approved On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->supplier_approved_on }}</div>
                    </td>
                    <th class="w-20">Supplier Approved Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->supplier_approved_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Supplier Approved to Obselete By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->supplier_approved_to_obselete_by }}</div>
                    </td>
                    <th class="w-20">Supplier Approved to Obselete On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->supplier_approved_to_obselete_on }}</div>
                    </td>
                    <th class="w-20">Supplier Approved to Obselete Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->supplier_approved_to_obselete_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">ReAudit By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->reAudit_by }}</div>
                    </td>
                    <th class="w-20">ReAudit On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->reAudit_on }}</div>
                    </td>
                    <th class="w-20">ReAudit Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->reAudit_comment }}</div>
                    </td>
                </tr>

                <tr>
                    <th class="w-20">Rejected By</th>
                    <td class="w-30">
                        <div class="static">{{ $data->rejectedDueToQuality_by }}</div>
                    </td>
                    <th class="w-20">Rejected On</th>
                    <td class="w-30">
                        <div class="static">{{ $data->rejectedDueToQuality_on }}</div>
                    </td>
                    <th class="w-20">Rejected Comment</th>
                    <td class="w-30">
                        <div class="static">{{ $data->rejectedDueToQuality_comment }}</div>
                    </td>
                </tr>

            </table>
        </div> -->


    </div>
    </div>

    <footer>
        <table>
            <tr>
                <td class="w-30">
                    <strong>Printed On :</strong> {{ date('d-M-Y') }}
                </td>
                <td class="w-40">
                    <strong>Printed By :</strong> {{ Auth::user()->name }}
                </td>
            </tr>
        </table>
    </footer>

</body>

</html>