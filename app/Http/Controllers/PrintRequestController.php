<?php

namespace App\Http\Controllers;

use App\Imports\DocumentsImport;
use App\Models\Annexure;
use App\Models\Department;
use App\Models\Division;
use App\Models\Document;
use App\Models\PrintRequest;
use App\Models\QMSDivision;
use Illuminate\Support\Facades\Hash;
use Helpers;
use App\Models\DocumentContent;
use App\Models\DocumentGridData;
//use App\Models\ContentsDocument;
use App\Models\DocumentHistory;
use App\Models\DocumentLanguage;
use App\Models\DocumentSubtype;
use App\Models\DocumentTraining;
//use App\Models\DocumentTraningInformation;
use App\Models\DocumentType;
use App\Models\DownloadControl;
use App\Models\DownloadHistory;
use App\Models\Grouppermission;
use App\Models\Keyword;
use App\Models\OpenStage;
use App\Models\PrintControl;
use App\Models\PrintHistory;
use App\Models\Process;
use App\Models\QMSProcess;
use App\Models\RoleGroup;
use App\Models\SetDivision;
use App\Models\Stage;
use App\Models\StageManage;
use App\Models\User;
use App\Services\DocumentService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class PrintRequestController extends Controller
{
    public function index(Request $request)
    {
        return view('frontend.print-request.index', compact('documents'));
    }

    public function create()
    {
        $division = SetDivision::where('user_id', Auth::id())->latest()->first();

        if (!empty($division)) {
            $division->dname = Division::where('id', $division->division_id)->value('name');
            $division->pname = Process::where('id', $division->process_id)->value('process_name');
            $process = QMSProcess::where([
                'process_name' => 'New Document',
                'division_id' => $division->division_id
            ])->first();
        } else {
            return "Division not found";
        }


        $users = User::all();
        if (!empty($users)) {
            foreach ($users as $data) {
                $data->role = RoleGroup::where('id', $data->role)->value('name');
            }
        }
        $document = Document::all();
        if (!empty($document)) {
            foreach ($document as $temp) {
                if (!empty($temp)) {
                    $temp->division = Division::where('id', $temp->division_id)->value('name');
                    $temp->typecode = DocumentType::where('id', $temp->document_type_id)->value('typecode');
                    $temp->year = Carbon::parse($temp->created_at)->format('Y');
                }
            }
        }

        $departments = Department::all();
        $documentTypes = DocumentType::all();
        $documentsubTypes = DocumentSubtype::all();
        $documentLanguages = DocumentLanguage::all();
        $documentList = Document::all();

        $hods = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', $process->id)
            ->where('user_roles.q_m_s_roles_id', 4)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();

        $qa = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', 89)
            ->where('user_roles.q_m_s_roles_id', 7)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();


        $usersValue = User::get();

        $reviewergroup = Grouppermission::where('role_id', 2)->get();
        $approversgroup = Grouppermission::where('role_id', 1)->get();
        $counter = DB::table('record_numbers')->value('counter');
        $recordNumber = str_pad($counter + 1, 5, '0', STR_PAD_LEFT);

        $user = User::all();

        return view('frontend.documents.print_request.create', compact(
            'departments',
            'documentTypes',
            'documentLanguages',
            'user',
            'hods',
            'reviewergroup',
            'approversgroup',
            'usersValue',
            'document',
            'users',
            'recordNumber',
            'division',
            'documentList',
            'qa',
            'documentsubTypes'
        ));
    }

    public function store(Request $request)
    {
        $printRequest = new PrintRequest();
        $printRequest->originator_id = Auth::id();
        $printRequest->division_id = $request->division_id;
        $printRequest->short_description = $request->short_desc;
        $printRequest->due_date = $request->due_dateDoc;
        $printRequest->permission_user_id = $request->permission_user_id;
        $printRequest->initiated_by = Auth::user()->id;
        $printRequest->initiated_on = Carbon::now()->format('d-M-Y');
        $printRequest->hods = $request->hods;
        $printRequest->qa = $request->qa;
        
        if (!empty($request->initial_attachments)) {
            $files = [];
            if ($request->hasfile('initial_attachments')) {
                foreach ($request->file('initial_attachments') as $file) {
                    $name = $request->name . 'initial_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }
            $printRequest->initial_attachments = json_encode($files);
        }
        $printRequest->stage = 1;
        $printRequest->status = 'Initiation';

        if (! empty($request->reference_records)) {
            $printRequest->reference_records = implode(',', $request->reference_records);
        }

        $printRequest->save();
        toastr()->success('Print Request Created');
        return redirect()->route('documents.index');
    }

    public function show($id)
    {
        $print_history = PrintRequest::find($id);
        $documentList = Document::all();
        $hods = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', 89)
            ->where('user_roles.q_m_s_roles_id', 4)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();

        $qa = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', 89)
            ->where('user_roles.q_m_s_roles_id', 7)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();


        $usersValue = User::get();
        // dd($print_history);
        return view('frontend.documents.print_request.edit', compact('usersValue', 'qa', 'hods','documentList', 'print_history'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $printRequest = PrintRequest::find($id);
        $printRequest->division_id = $request->division_id;
        $printRequest->short_description = $request->short_description;
        $printRequest->due_date = $request->due_dateDoc;
        $printRequest->permission_user_id = $request->permission_user_id;
        // $printRequest->initiated_by = Auth::user()->id;
        // $printRequest->initiated_on = Carbon::now()->format('d-M-Y');
        if($printRequest->stage == 2){
            $printRequest->hods = $request->hods;
            $printRequest->qa = $request->qa;
        }
        if (!empty($request->initial_attachments)) {
            $files = [];
            if ($request->hasfile('initial_attachments')) {
                foreach ($request->file('initial_attachments') as $file) {
                    $name = $request->name . 'initial_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }
            $printRequest->initial_attachments = json_encode($files);
        }

        if (! empty($request->reference_records)) {
            $printRequest->reference_records = implode(',', $request->reference_records);
        }
        
        if($printRequest->stage == 2){
                    $printRequest->hod_remarks = $request->hod_remarks;
                    $printRequest->hod_on = Carbon::now()->format('d-M-Y');
                    $printRequest->hod_by = Auth::user()->id;
                    if (!empty ($request->hod_attachments)) {
                        $files = [];
                        if ($printRequest->hod_attachments) {
                            $existingFiles = json_decode($printRequest->hod_attachments, true); // Convert to associative array
                            if (is_array($existingFiles)) {
                                $files = $existingFiles;
                            }
                        }
                        if ($request->hasfile('hod_attachments')) {
                            foreach ($request->file('hod_attachments') as $file) {
                                $name = $request->name . 'hod_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                                $file->move('upload/', $name);
                                $files[] = $name;
                            }
                        }
                        $printRequest->hod_attachments = json_encode($files);
                    }
                }
                if($printRequest->stage == 3){
                    $printRequest->qa_remarks = $request->qa_remarks;
                    $printRequest->qa_on = Carbon::now()->format('d-M-Y');
                    $printRequest->qa_by = Auth::user()->id;
                    if (!empty ($request->qa_attachments)) {
                        $files = [];
                        if ($printRequest->qa_attachments) {
                            $existingFiles = json_decode($printRequest->qa_attachments, true); // Convert to associative array
                            if (is_array($existingFiles)) {
                                $files = $existingFiles;
                            }
                        }
                        if ($request->hasfile('qa_attachments')) {
                            foreach ($request->file('qa_attachments') as $file) {
                                $name = $request->name . 'qa_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                                $file->move('upload/', $name);
                                $files[] = $name;
                            }
                        }
                        $printRequest->qa_attachments = json_encode($files);
                    }
                }

        $printRequest->update();
        toastr()->success('Print Request Created');
        return back();
    }

    public function stageChange(Request $request, $id)
    {
        // dd($request->all());
        if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
            $ErrataControl = PrintRequest::find($id);
            $lastDocument = PrintRequest::find($id);
            // $evaluation = Evaluation::where('cc_id', $id)->first();
            if ($ErrataControl->stage == 1) {
                $ErrataControl->stage = "2";
                $ErrataControl->status = "Under HOD Approval";
                $ErrataControl->initiated_comment = $request->comment;

                // $history = new ErrataAuditTrail();
                // $history->errata_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->current = $ErrataControl->submitted_by;
                // $history->comment = $request->comment;
                // $history->action = 'Submit';
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->change_to =   "Pending Review";
                // $history->change_from = $lastDocument->status;
                // $history->action_name = 'Not Applicable';
                // $history->stage = 'Pending Review';
                // $history->save();

                $ErrataControl->update();
                toastr()->success('Request Sent');
                return back();
            }
            if ($ErrataControl->stage == 2) {
                $ErrataControl->stage = "3";
                $ErrataControl->status = "Under QA Approval";
                $ErrataControl->hod_sig_by = Auth::user()->id;
                $ErrataControl->hod_sig_on = Carbon::now()->format('d-M-Y');
                $ErrataControl->hod_comment = $request->comment;

                // $history = new ErrataAuditTrail();
                // $history->errata_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->current = $ErrataControl->review_completed_by;
                // $history->comment = $request->comment;
                // $history->action = 'Review Complete';
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->change_to =   "Pending Correction";
                // $history->change_from = $lastDocument->status;
                // $history->stage = 'Pending Correction';
                // $history->action_name = 'Not Applicable';
                // $history->save();

                // $ErrataControl->status = "Pending Correction";
                $ErrataControl->update();
                toastr()->success('Request Sent');
                return back();
            }
            if ($ErrataControl->stage == 3) {
                $ErrataControl->stage = "4";
                $ErrataControl->status = "Approved";
                $ErrataControl->qa_sig_by = Auth::user()->id;
                $ErrataControl->qa_sig_on = Carbon::now()->format('d-M-Y');
                $ErrataControl->qa_comment = $request->comment;

                // $history = new ErrataAuditTrail();
                // $history->errata_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->current = $ErrataControl->correction_completed_by;
                // $history->comment = $request->comment;
                // $history->action = 'Correction Completed';
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->change_to =   "Pending HOD Review";
                // $history->change_from = $lastDocument->status;
                // $history->stage = 'Pending HOD Review';
                // $history->action_name = 'Not Applicable';
                // $history->save();

                // $ErrataControl->status = "Pending HOD Review";
                $ErrataControl->update();
                toastr()->success('Document Sent');
                return back();
            }
        } else {
            toastr()->error('E-signature Not match');
            return back();
        }
    }
    public function stageReject(Request $request, $id)
    {
        // $ErrataControl = errata::find($id);

        if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
            $ErrataControl = PrintRequest::find($id);
            $lastDocument = PrintRequest::find($id);
            if ($ErrataControl->stage == 2) {
                $ErrataControl->stage = "1";
                // $ErrataControl->hod_by = Auth::user()->id;
                // $ErrataControl->hod_on = Carbon::now()->format('d-M-Y');
                // $ErrataControl->hod_comment = $request->comment;
                $ErrataControl->status = "Initiation";

                // $history = new ErrataAuditTrail();
                // $history->errata_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->current = $ErrataControl->hod_review_complete_by;
                // $history->comment = $request->comment;
                // $history->action = 'Reject';
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->change_to =   "Opened";
                // $history->change_from = $lastDocument->status;
                // $history->action_name = 'Not Applicable';
                // $history->stage = 'Opened';
                // $history->save();

                $ErrataControl->update();
                toastr()->success('Document Sent');
                return back();
            }
            if ($ErrataControl->stage == 3) {
                $ErrataControl->stage = "2";
                $ErrataControl->status = "Under HOD Approval";
                // $ErrataControl->qa_by = Auth::user()->id;
                // $ErrataControl->qa_on = Carbon::now()->format('d-M-Y');
                // $ErrataControl->qa_comment = $request->comment;

                // $history = new ErrataAuditTrail();
                // $history->errata_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->current = $ErrataControl->sent_to_open_state_by;
                // $history->comment = $request->comment;
                // $history->action = 'Request More Info.';
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->change_to =   "Pending Review";
                // $history->change_from = $lastDocument->status;
                // $history->action_name = 'Not Applicable';
                // $history->stage = 'Pending Review';
                // $history->save();

                $ErrataControl->update();
                toastr()->success('Document Sent');
                return back();
            }
        } else {
            toastr()->error('E-signature Not match');
            return back();
        }
    }

    public function erratacancelstage(Request $request, $id)
    {
        if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
            $ErrataControl = PrintRequest::find($id);
            $lastDocument = PrintRequest::find($id);
                $ErrataControl->stage = "0";
                $ErrataControl->status = "Closed-Cancelled";
                $ErrataControl->reject_by = Auth::user()->id;
                $ErrataControl->reject_on = Carbon::now()->format('d-M-Y');
                $ErrataControl->reject_comment = $request->comment;

                // $ErrataControl->sent_to_open_state_by = Auth::user()->id;
                // $ErrataControl->sent_to_open_state_on = Carbon::now()->format('d-M-Y');
                // $ErrataControl->sent_to_open_state_comment = $request->comment;
                // $history = new ErrataAuditTrail();
                // $history->errata_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->current = $ErrataControl->sent_to_open_state_by;
                // $history->comment = $request->comment;
                // $history->action = 'Cancel';
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->change_to =   "Closed-Cancelled";
                // $history->change_from = $lastDocument->status;
                // $history->action_name = 'Not Applicable';
                // $history->stage = 'Closed-Cancelled';
                // $history->save();

                $ErrataControl->update();
                toastr()->success('Document Sent');
                return back();
            }
            else {
            toastr()->error('E-signature Not match');
            return back();
        }
    }

    public function singleReport($id){
        $data = PrintRequest::find($id);
        if (!empty($data)) {
            $data->originator = User::where('id', $data->originator_id)->value('name'); 
            $ids = explode(',', $data->reference_records);  
            $relatedRecords = Document::whereIn('id', $ids)->pluck('document_name')->toArray();    

            $pdf = App::make('dompdf.wrapper');
            $time = Carbon::now();
            $pdf = PDF::loadview('frontend.documents.print_request.single-report', compact(
                'data',
                'relatedRecords'
            ))
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                ]);
            $pdf->setPaper('A4');
            $pdf->render();
            $canvas = $pdf->getDomPDF()->getCanvas();
            $height = $canvas->get_height();
            $width = $canvas->get_width();

            $canvas->page_script('$pdf->set_opacity(0.1,"Multiply");');

            $canvas->page_text(
                $width / 4,
                $height / 2,
                $data->status,
                null,
                25,
                [0, 0, 0],
                2,
                6,
                -20
            );
            return $pdf->stream('Print Request' . $id . '.pdf');
        }
    }

    public function printHistories($id){
        $data = PrintHistory::where('document_id', $id)->get();
        return view('frontend.documents.print_histories', compact('data'));
    }
}
