<?php

namespace App\Http\Controllers;

use App\Imports\DocumentsImport;
use App\Models\Annexure;
use App\Models\DocumentAnnexure;
use App\Models\Department;
use App\Models\Division;
use App\Models\Document;
use App\Models\QMSDivision;
use App\Models\PrintPdfHistory;

use Helpers;
use App\Models\DocumentContent;
use App\Models\DocumentGridData;
//use App\Models\ContentsDocument;
use App\Models\DocumentHistory;
use App\Models\DocumentLanguage;
use App\Models\DocumentSubtype;
use App\Models\DocumentTraining;
use App\Models\CC;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;
use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\View;
use DOMDocument;

use Illuminate\Support\Facades\Response;


class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function division(Request $request)
    {
        if ($request->process_id == 'print-request') {
            $division_Check = $request->division_id;
            return redirect()->route('print_request.create', compact('division_Check'));
        }
        $new = new SetDivision;
        $new->division_id = $request->division_id;
        $new->process_id = $request->process_id;
        $new->user_id = Auth::user()->id;
        $new->save();
        //return redirect()->route('documents.create');
        $id = $request->process_id;
        return redirect()->route('documents.create', compact('id'));
    }
    public function division_old(Request $request)
    {

        // return $request;

        $new = new Document;
        $new->originator_id = $request->originator_id;
        $new->division_id = $request->division_id;

        $new->process_id = $request->process_id;
        $new->record = $request->record;
        $new->revised = $request->revised;
        $new->revised_doc = $request->revised_doc;
        $new->document_name = $request->document_name;
        $new->short_description = $request->short_description;
        $new->due_dateDoc = $request->due_dateDoc;
        $new->sop_type = $request->sop_type;
        $new->description = $request->description;
        $new->notify_to = json_encode($request->notify_to);
        $new->reference_record = $request->reference_record;
        $new->department_id = $request->department_id;
        $new->document_type_id = $request->document_type_id;
        $new->document_subtype_id = $request->document_subtype_id;
        $new->document_language_id = $request->document_language_id;
        $new->keywords = $request->keywords;
        $new->effective_date = $request->effective_date;
        $new->next_review_date = $request->next_review_date;
        $new->review_period = $request->review_period;
        $new->attach_draft_doocument = $request->attach_draft_doocument;
        $new->attach_effective_docuement = $request->attach_effective_docuement;
        $new->reviewers = $request->reviewers;
        $new->approvers = $request->approvers;
        $new->reviewers_group = $request->reviewers_group;
        $new->approver_group = $request->approver_group;
        $new->revision_summary = $request->revision_summary;
        $new->revision_type = $request->revision_type;
        $new->major = $request->major;
        $new->minor = $request->minor;
        $new->stage = $request->stage;
        $new->status = $request->status;
        $new->document = $request->document;
        $new->revision = $request->revision;
        $new->revision_policy = $request->revision_policy;
        $new->training_required = $request->training_required;
        $new->trainer = $request->trainer;
        $new->comments = $request->comments;

        $new->user_id = Auth::user()->id;
        $new->save();

        return redirect()->route('documents.create');
    }

    public function dcrDivision()
    {
        return redirect()->route('change-control.create');
    }
    public function index(Request $request)
    {

        $query = Document::query();
        $query->where('originator_id', auth()->id());


        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }
        if ($request->has('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->has('originator_id')) {
            $query->where('originator_id', $request->originator_id);
        }
        $count = $query->where('documents.originator_id', Auth::user()->id)->count();
        $documents = $query->paginate(10);


        $divisions = QMSDivision::where('status', '1')->select('id', 'name')->get();
        // $divisions = QMSDivision::where('status', '1')->select('id', 'name')->get();
        $documentValues = Document::withoutTrashed()->select('id', 'document_type_id')->get();
        $documentTypeIds = $documentValues->pluck('document_type_id')->unique()->toArray();
        $documentTypes = DocumentType::whereIn('id', $documentTypeIds)->select('id', 'name')->get();
        $document_type = Document::where('document_type_id', $documentTypeIds)->get();
        $documentStatus = Document::withoutTrashed()->select('id', 'status')->get();
        $documentStatusIds = $documentValues->pluck('document_type_id')->unique()->toArray();
        // dd($documentStatusIds);
        $OriValues = Document::withoutTrashed()->select('id', 'originator_id')->get();
        $OriTypeIds = $OriValues->pluck('originator_id')->unique()->toArray();
        $originator = User::whereIn('id', $OriTypeIds)->select('id', 'name')->get();

        // return $documents;

        // $count = Document::where('documents.originator_id', Auth::user()->id)->count();
        // $documents = Document::join('users', 'documents.originator_id', 'users.id')->join('document_types', 'documents.document_type_id', 'document_types.id')
        //     ->join('divisions', 'documents.division_id', 'divisions.id')
        //     ->select('documents.*', 'users.name as originator_name', 'document_types.name as document_type_name', 'divisions.name as division_name')->where('documents.originator_id', Auth::user()->id)->orderByDesc('documents.id')->paginate(10);
        return view('frontend.documents.index', compact('documents', 'count', 'divisions', 'document_type', 'originator', 'documentTypes', 'documentValues', 'documentStatus'));
    }

    public function filterRecord(Request $request)
    {
        $res = [];

        $query = Document::query();

        if ($request->status && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->document_type_id && !empty($request->document_type_id)) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->documentTypes && !empty($request->division_id)) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->originator_id && !empty($request->originator_id)) {
            $query->where('originator_id', $request->originator_id);
        }

        $documents = $query->get();

        foreach ($documents as $doc) {
            $doctype = DocumentType::where('id', $doc->document_type_id)->value('name');
            $originatorName = User::where('id', $doc->originator_id)->value('name');

            // Assign the retrieved names to the document object
            $doc['document_type_name'] = $doctype;
            $doc['originator_name'] = $originatorName;
        }

        $html = view('frontend.documents.comps.record_table', compact('documents'))->render();

        $res['html'] = $html;

        return response()->json($res);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function reviseCreate()
    {
        //
        $division = SetDivision::where('user_id', Auth::id())->latest()->first();
        if (!empty($division)) {
            $division->dname = Division::where('id', $division->division_id)->value('name');
            $division->pname = Process::where('id', $division->process_id)->value('process_name');
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
        //$reviewer = User::get();
        $reviewer = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 89)
            ->where('user_roles.q_m_s_roles_id', 2)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        $trainer = User::get();


        // $approvers = DB::table('user_roles')
        // ->join('users', 'user_roles.user_id', '=', 'users.id')
        // ->where('user_roles.q_m_s_processes_id', 89)
        // ->where('q_m_s_roles_id', 1)
        // ->get();;

        $approvers = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 89)
            ->where('user_roles.q_m_s_roles_id', 1)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();

        $hods = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 89)
            ->where('user_roles.q_m_s_roles_id', 4)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();

        return $hods;


        $reviewergroup = Grouppermission::where('role_id', 2)->get();
        $approversgroup = Grouppermission::where('role_id', 1)->get();
        // Retrieve the current counter value
        $counter = DB::table('record_numbers')->value('counter');

        // Generate the record number with leading zeros
        $recordNumber = str_pad($counter + 1, 5, '0', STR_PAD_LEFT);

        $user = User::all();

        return view('frontend.documents.create', compact(
            'departments',
            'documentTypes',
            'documentLanguages',
            'user',
            'reviewer',
            'approvers',
            'hods',
            'reviewergroup',
            'approversgroup',
            'trainer',
            'document',
            'users',
            'recordNumber',
            'division',
            'documentsubTypes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $division = SetDivision::where('user_id', Auth::id())->latest()->first();
        $ccrecord = CC::get();

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
        //$reviewer = User::get();
        $reviewer = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', $process->id)
            ->where('user_roles.q_m_s_roles_id', 2)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();



        $approvers = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', $process->id)
            ->where('user_roles.q_m_s_roles_id', 1)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();

        $hods = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->where('user_roles.q_m_s_processes_id', $process->id)
            ->where('user_roles.q_m_s_roles_id', 4)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name')
            ->get();

        $drafter = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', $process->id)
            ->where('user_roles.q_m_s_roles_id', 40)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();

        $qa = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', $process->id)
            ->where('user_roles.q_m_s_roles_id', 7)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();

        $trainer = User::get();

        $reviewergroup = Grouppermission::where('role_id', 2)->get();
        $approversgroup = Grouppermission::where('role_id', 1)->get();
        // Retrieve the current counter value
        $counter = DB::table('record_numbers')->value('counter');

        // Generate the record number with leading zeros
        $recordNumber = str_pad($counter + 1, 5, '0', STR_PAD_LEFT);

        $user = User::all();

        return view('frontend.documents.create', compact(
            'departments',
            'documentTypes',
            'documentLanguages',
            'user',
            'reviewer',
            'drafter',
            'ccrecord',
            'approvers',
            'hods',
            'reviewergroup',
            'approversgroup',
            'trainer',
            'document',
            'users',
            'recordNumber',
            'division',
            'qa',
            'documentsubTypes'
        ));
    }

    // documentExportPDF
    public function documentExportPDF()
    {
        $documents = Document::all();
    }

    // documentExportEXCEL
    public function documentExportEXCEL()
    {
        return Excel::download(new DocumentsExport, 'documents.csv');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // effective_date, review_period

        if ($request->submit == 'save') {

            $document = new Document();

            $division = SetDivision::where('user_id', Auth::id())->latest()->first();

            if (empty($request->division_id) && empty($request->process_id)) {
                $document->division_id = $division->division_id;
                $document->process_id = $division->process_id;
            } else {
                $document->division_id = $request->division_id;
                $document->process_id = $request->process_id;
            }

            $document->record = DB::table('record_numbers')->value('counter') + 1;
            $document->originator_id = Auth::id();
            $document->legacy_number = $request->legacy_number;
            $document->document_name = $request->document_name;
            $document->short_description = $request->short_desc;
            $document->description = $request->description;
            $document->stage = 1;
            $document->status = Stage::where('id', 1)->value('name');
            $document->due_dateDoc = $request->due_dateDoc;
            $document->priodic_review = $request->priodic_review;
            $document->department_id = $request->department_id;
            $document->document_type_id = $request->document_type_id;
            $document->document_subtype_id = $request->document_subtype_id;
            $document->document_language_id = $request->document_language_id;
            $document->effective_date = $request->effective_date;

            $document->cc_reference_record = $request->cc_reference_record == null ? null : implode(',', $request->cc_reference_record);

            try {
                if ($request->effective_date) {
                    $next_review_date = Carbon::parse($request->effective_date)->addYears($request->review_period)->format('Y-m-d');
                    $document->next_review_date = $next_review_date;
                }
            } catch (\Exception $e) {
                // 
            }

            $document->review_period = $request->review_period;
            $document->training_required = $request->training_required;
            $document->trainer = $request->trainer;
            $document->comments = $request->comments;
            $document->revision_type = $request->revision_type;
            $document->major = $request->major;
            $document->division_id = $request->division_id;

            $document->minor = $request->minor;
            $document->sop_type = $request->sop_type;
            $document->notify_to = json_encode($request->notify_to);

            $document->initiated_by = Auth::user()->id;
            $document->initiated_on = now();

            if (!empty($request->initial_attachments)) {
                $files = [];
                if ($request->hasfile('initial_attachments')) {
                    foreach ($request->file('initial_attachments') as $file) {
                        $name = $request->name . 'initial_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                        $file->move('upload/', $name);
                        $files[] = $name;
                    }
                }
                $document->initial_attachments = json_encode($files);
            }


            if ($request->keywords) {
                $document->keywords = implode(',', $request->keywords);
            }

            if ($request->reference_record) {
                $document->reference_record = implode(',', $request->reference_record);
            }

            if ($request->hasfile('attach_draft_doocument')) {

                $image = $request->file('attach_draft_doocument');

                $ext = $image->getClientOriginalExtension();

                $image_name = date('y-m-d') . '-' . rand() . '.' . $ext;

                $image->move('upload/document/', $image_name);

                $document->attach_draft_doocument = $image_name;
            }

            if ($request->hasfile('attach_effective_docuement')) {

                $image = $request->file('attach_effective_docuement');

                $ext = $image->getClientOriginalExtension();

                $image_name = date('y-m-d') . '-' . rand() . '.' . $ext;

                $image->move('upload/document/', $image_name);

                $document->attach_effective_docuement = $image_name;
            }

            $document->revision_summary = $request->revision_summary;
            if (!empty($request->reviewers)) {
                $document->reviewers = implode(',', $request->reviewers);
            }
            if (!empty($request->approvers)) {
                $document->approvers = implode(',', $request->approvers);
            }
            if (!empty($request->qa)) {
                $document->qa = implode(',', $request->qa);
            }
            if (!empty($request->hods)) {
                $document->hods = implode(',', $request->hods);
            }
            if (!empty($request->drafters)) {
                $document->drafters = implode(',', $request->drafters);
            }
            if (!empty($request->reviewers_group)) {
                $document->reviewers_group = implode(',', $request->reviewers_group);
            }
            if (!empty($request->approver_group)) {
                $document->approver_group = implode(',', $request->approver_group);
            }
            $document->save();

            foreach ($request->annexuredata as $index => $annData)
            {
                $documentannexure = new DocumentAnnexure();
                $documentannexure->document_id = $document->id;
                $documentannexure->version = $index+1;       
                $documentannexure->content = $annData;
                $documentannexure->save();
            }

            DocumentService::update_document_numbers();

            if ($document) {
                DocumentService::handleDistributionGrid($document, $request->distribution);
            }

            // Retrieve the current counter value
            $counter = DB::table('record_numbers')->value('counter');

            // Generate the record number with leading zeros
            $recordNumber = str_pad($counter, 5, '0', STR_PAD_LEFT);

            // Increment the counter value
            $newCounter = $counter + 1;
            DB::table('record_numbers')->update(['counter' => $newCounter]);
            if (!empty($request->keywords)) {
                foreach ($request->keywords as $key) {
                    $keyword = new Keyword();
                    $keyword->user_id = Auth::user()->id;
                    $keyword->document_id = $document->id;
                    $keyword->keyword = $key;
                    $keyword->save();
                }
            }
            if ($request->training_required == 'yes') {
                $trainning = new DocumentTraining();
                $trainning->document_id = $document->id;
                $trainning->trainer = $request->trainer;
                $trainning->cbt = $request->cbt;
                $trainning->type = $request->type;
                $trainning->comments = $request->comments;
                $trainning->save();
            }

            $annexure = new Annexure();
            $annexure->document_id = $document->id;
            if (!empty($request->serial_number)) {
                $annexure->sno = serialize($request->serial_number);
            }
            if (!empty($request->annexure_number)) {
                $annexure->annexure_no = serialize($request->annexure_number);
            }
            if (!empty($request->annexure_data)) {
                $annexure->annexure_title = serialize($request->annexure_data);
            }
            $annexure->save();


            $content = new DocumentContent();
            $content->document_id = $document->id;
            $content->purpose = $request->purpose;
            $content->scope = $request->scope;
            $content->procedure = $request->procedure;
            $content->safety_precautions = $request->safety_precautions;
            $content->hod_comments = $request->hod_comments;

            if ($request->has('hod_attachments') && $request->hasFile('hod_attachments')) {
                $files = [];

                foreach ($request->file('hod_attachments') as $file) {
                    $name = $request->name . '-hod_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }

                $content->hod_attachments = json_encode($files);
            }


            if (! empty($request->materials_and_equipments)) {
                $content->materials_and_equipments = serialize($request->materials_and_equipments);
            }
            if (!empty($request->responsibility)) {
                $content->responsibility = serialize($request->responsibility);
            }
            if (!empty($request->accountability)) {
                $content->accountability = serialize($request->accountability);
            }
            if (!empty($request->abbreviation)) {
                $content->abbreviation = serialize($request->abbreviation);
            }
            if (!empty($request->defination)) {
                $content->defination = serialize($request->defination);
            }
            if (!empty($request->reporting)) {
                $content->reporting = serialize($request->reporting);
            }
            if (!empty($request->references)) {
                $content->references = serialize($request->references);
            }
            if (!empty($request->ann)) {
                $content->ann = serialize($request->ann);
            }
            // if ($request->hasfile('references')) {

            //     $image = $request->file('references');

            //     $ext = $image->getClientOriginalExtension();

            //     $image_name = date('y-m-d').'-'.rand().'.'.$ext;

            //     $image->move('upload/document/', $image_name);

            //     $content->references = $image_name;
            // }
            if (!empty($request->ann)) {
                $content->ann = serialize($request->ann);
            }
            if (!empty($request->annexuredata)) {
                $content->annexuredata = serialize($request->annexuredata);
            }
            if (!empty($request->distribution)) {
                $content->distribution = serialize($request->distribution);
            }

            $content->save();


            $annexure_data = $request->input('annexuredata');
                    $document->doc_content->annexuredata = serialize($annexure_data);
                    $document->doc_content->save();

            toastr()->success('Document created');

            return redirect()->route('documents.index');
        } else {
            toastr()->error('Not working');

            return redirect()->back();
        }


        
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $ccrecord = CC::get();

        $users = User::all();
        if (!empty($users)) {
            foreach ($users as $data) {
                $data->role = RoleGroup::where('id', $data->role)->value('name');
            }
        }
        $document_data = Document::all();
        if (!empty($document_data)) {
            foreach ($document_data as $temp) {
                if (!empty($temp)) {
                    $temp->division = Division::where('id', $temp->division_id)->value('name');
                    $temp->typecode = DocumentType::where('id', $temp->document_type_id)->value('typecode');
                    $temp->year = Carbon::parse($temp->created_at)->format('Y');
                }
            }
        }

        $PH = PrintHistory::where('document_id', $id)->get();
        // $PH = PrintHistory::where('document_id', $id)->with('department')->get();

        // foreach($PH as $p)
        // return $p;

        $print_history = PrintHistory::join('users', 'print_histories.user_id', 'users.id')->select('print_histories.*', 'users.name as user_name')->where('document_id', $id)->get();
        $document = Document::find($id);
        
        $document_annexures = DocumentAnnexure::where([
            'document_id' => $id,
            'is_child' => 0
        ])->get();

        $document->date = Carbon::parse($document->created_at)->format('d-M-Y');
        $document['document_content'] = DocumentContent::where('document_id', $id)->first();
        $document_distribution_grid = PrintHistory::where('document_id', $id)->leftjoin('documents', 'documents.id', 'print_histories.document_id')->get(['print_histories.*', 'documents.document_name']);

        $document['division'] = Division::where('id', $document->division_id)->value('name');
        $year = Carbon::parse($document->created_at)->format('Y');
        $trainer = User::get();
        $trainingDoc = DocumentTraining::where('document_id', $id)->first();
        $history = DocumentHistory::where('document_id', $id)->get();
        $documentsubTypes = DocumentSubtype::all();
        $keywords = Keyword::where('document_id', $id)->get();
        $annexure = Annexure::where('document_id', $id)->first();

        $signature = StageManage::where('document_id', $id)->get();
        //$reviewer = User::get();
        $reviewer = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 2)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        //$approvers = User::get();
        $approvers = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 1)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        $reviewergroup = Grouppermission::where('role_id', 2)->get();
        $approversgroup = Grouppermission::where('role_id', 1)->get();
        $user = User::all();
        $departments = Department::all();
        // foreach ($departments as $d)
        // return $departments;
        $documentTypes = DocumentType::all();
        $documentLanguages = DocumentLanguage::all();

        $hods = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 4)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        $qa = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 7)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();

        $drafter = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 40)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();




        return view('frontend.documents.edit', compact(
            'document',
            'departments',
            'documentTypes',
            'documentLanguages',
            'reviewer',
            'approvers',
            'hods',
            'drafter',
            'reviewergroup',
            'approversgroup',
            'year',
            'print_history',
            'signature',
            'trainer',
            'document_data',
            'users',
            'trainingDoc',
            'history',
            'keywords',
            'qa',
            'ccrecord',
            'annexure',
            'documentsubTypes',
            'document_distribution_grid',
            'document_annexures',
            'PH'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {

        if ($request->submit == 'save') {
            $lastDocument = Document::find($id);
            $lastContent = DocumentContent::firstOrNew([
                'document_id' => $id
            ]);
            $lastTraining = DocumentTraining::where('document_id', $id)->first();
            $document = Document::find($id);
            if ($document->stage <= 7) {
                $document->document_name = $request->document_name;
                $document->short_description = $request->short_desc;
                $document->description = $request->description;


                $document->legacy_number = $request->legacy_number;
                $document->due_dateDoc = $request->due_dateDoc;
                $document->priodic_review = $request->priodic_review;
                $document->sop_type = $request->sop_type;
                $document->department_id = $request->department_id;
                $document->document_type_id = $request->document_type_id;
                $document->document_subtype_id = $request->document_subtype_id;
                $document->document_language_id = $request->document_language_id;
                $document->training_required = $request->training_required;
                $document->attach_draft_doocument = $request->attach_draft_doocument;
                $document->cc_reference_record = implode(',', $request->cc_reference_record);
                $document->notify_to = json_encode($request->notify_to);

                if (!empty($request->initial_attachments)) {
                    $files = [];
                    if ($request->hasfile('initial_attachments')) {
                        foreach ($request->file('initial_attachments') as $file) {
                            $name = $request->name . 'initial_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                            $file->move('upload/', $name);
                            $files[] = $name;
                        }
                    }
                    $document->initial_attachments = json_encode($files);
                }

                if ($request->keywords) {
                    $document->keywords = implode(',', $request->keywords);
                }

                if (is_array($request->notify_to)) {
                    $document->notify_to = implode(',', $request->notify_to);
                }

                if ($request->reference_record) {
                    $document->reference_record = implode(',', $request->reference_record);
                }


                if ($request->hasfile('attach_draft_doocument')) {

                    $image = $request->file('attach_draft_doocument');

                    $ext = $image->getClientOriginalExtension();

                    $image_name = date('y-m-d') . '-' . rand() . '.' . $ext;

                    $image->move('upload/document/', $image_name);

                    $document->attach_draft_doocument = $image_name;
                }

                if ($request->hasfile('attach_effective_docuement')) {

                    $image = $request->file('attach_effective_docuement');

                    $ext = $image->getClientOriginalExtension();

                    $image_name = date('y-m-d') . '-' . rand() . '.' . $ext;

                    $image->move('upload/document/', $image_name);

                    $document->attach_effective_docuement = $image_name;
                }
                $document->revision_summary = $request->revision_summary;
                $document->revision_type = $request->revision_type;
                $document->major = $request->major;
                $document->minor = $request->minor;

                if ($document->stage == 2) {
                    $document->drafter_remarks = $request->drafter_remarks;
                    if (!empty($request->drafter_attachments)) {
                        $files = [];
                        if ($document->drafter_attachments) {
                            $existingFiles = json_decode($document->drafter_attachments, true); // Convert to associative array
                            if (is_array($existingFiles)) {
                                $files = $existingFiles;
                            }
                        }
                        if ($request->hasfile('drafter_attachments')) {
                            foreach ($request->file('drafter_attachments') as $file) {
                                $name = $request->name . 'drafter_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                                $file->move('upload/', $name);
                                $files[] = $name;
                            }
                        }
                        $document->drafter_attachments = json_encode($files);
                    }
                }
                if ($document->stage == 3) {
                    $document->hod_remarks = $request->hod_remarks;
                    if (!empty($request->hod_attachments)) {
                        $files = [];
                        if ($document->hod_attachments) {
                            $existingFiles = json_decode($document->hod_attachments, true);
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
                        $document->hod_attachments = json_encode($files);
                    }
                }
                if ($document->stage == 4) {
                    $document->qa_remarks = $request->qa_remarks;
                    if (!empty($request->qa_attachments)) {
                        $files = [];
                        if ($document->qa_attachments) {
                            $existingFiles = json_decode($document->qa_attachments, true);
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
                        $document->qa_attachments = json_encode($files);
                    }
                }
                if ($document->stage == 5) {
                    $document->reviewer_remarks = $request->reviewer_remarks;
                    if (!empty($request->reviewer_attachments)) {
                        $files = [];
                        if ($document->reviewer_attachments) {
                            $existingFiles = json_decode($document->reviewer_attachments, true); // Convert to associative array
                            if (is_array($existingFiles)) {
                                $files = $existingFiles;
                            }
                        }
                        if ($request->hasfile('reviewer_attachments')) {
                            foreach ($request->file('reviewer_attachments') as $file) {
                                $name = $request->name . 'reviewer_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                                $file->move('upload/', $name);
                                $files[] = $name;
                            }
                        }
                        $document->reviewer_attachments = json_encode($files);
                    }
                }
                if ($document->stage == 6) {
                    $document->approver_remarks = $request->approver_remarks;
                    if (!empty($request->approver_attachments)) {
                        $files = [];
                        if ($document->approver_attachments) {
                            $existingFiles = json_decode($document->approver_attachments, true); // Convert to associative array
                            if (is_array($existingFiles)) {
                                $files = $existingFiles;
                            }
                        }
                        if ($request->hasfile('approver_attachments')) {
                            foreach ($request->file('approver_attachments') as $file) {
                                $name = $request->name . 'approver_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                                $file->move('upload/', $name);
                                $files[] = $name;
                            }
                        }
                        $document->approver_attachments = json_encode($files);
                    }
                }


                if (!empty($request->reviewers)) {
                    $document->reviewers = implode(',', $request->reviewers);
                }
                if (!empty($request->approvers)) {
                    $document->approvers = implode(',', $request->approvers);
                }
                if (!empty($request->hods)) {
                    $document->hods = implode(',', $request->hods);
                }
                if (!empty($request->qa)) {
                    $document->qa = implode(',', $request->qa);
                }
                if (!empty($request->drafters)) {
                    $document->drafters = implode(',', $request->drafters);
                }
                if (!empty($request->reviewers_group)) {
                    $document->reviewers_group = implode(',', $request->reviewers_group);
                }
                if (!empty($request->approver_group)) {
                    $document->approver_group = implode(',', $request->approver_group);
                }
            }

            
            
              // Save the annexure data
        $annexure_data = [];
       

        $document->annexuredata = serialize($annexure_data);
        // $document->save();

            $document->update();



            DocumentService::handleDistributionGrid($document, $request->distribution);

            $existing_keywords = Keyword::where('document_id', $document->id)->get();

            foreach ($existing_keywords as $existing_keyword) {
                $existing_keyword->delete();
            }

            if (!empty($request->keywords)) {

                foreach ($request->keywords as $key) {
                    $keyword = new Keyword();
                    $keyword->user_id = Auth::user()->id;
                    $keyword->document_id = $document->id;
                    $keyword->keyword = $key;
                    $keyword->save();
                }
            }




            if ($request->training_required == 'yes') {
                $trainning = DocumentTraining::where('document_id', $id)->first();

                if (!$request->trainer) {
                    toastr()->error('Trainer not selected!');
                    return back();
                }

                if (empty($trainning)) {
                    $trainning = new DocumentTraining();
                    $trainning->document_id = $document->id;
                    $trainning->trainer = $request->trainer;
                    $trainning->cbt = $request->cbt;
                    $trainning->type = $request->type;
                    $trainning->comments = $request->comments;
                    $trainning->save();
                } else {
                    $trainning->document_id = $document->id;
                    $trainning->trainer = $request->trainer;
                    $trainning->cbt = $request->cbt;
                    $trainning->type = $request->type;
                    $trainning->comments = $request->comments;
                    $trainning->update();
                }
            }
            if ($lastDocument->document_name != $document->document_name || !empty($request->document_name_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Document Name';
                $history->previous = $lastDocument->document_name;
                $history->current = $document->document_name;

                $stage = $request->stage; // Assuming stage is being passed in the request

                if (is_array($request->document_name_comment)) {
                    if ($stage >= 5 && $stage <= 11) {
                        // Custom handling for stages 5 to 11
                        $history->comment = implode(',', $request->document_name_comment);
                    } else {
                        // Normal handling for other stages
                        $history->comment = implode(',', $request->document_name_comment);
                    }
                } else {
                    // Handle the case where document_name_comment is not an array
                    $history->comment = $request->document_name_comment;
                }

                // $history->comment = implode($request->document_name_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
                $changeControl = OpenStage::where('title', $lastDocument->document_name)->first();
                if ($changeControl) {
                    $changeControl->title = $document->document_name;
                    $changeControl->update();
                }
            }
            if ($lastDocument->short_description != $document->short_description || !empty($request->short_desc_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Short Description';
                $history->previous = $lastDocument->short_description;
                $history->current = $document->short_description;
                $history->comment = implode($request->short_desc_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->due_dateDoc != $document->due_dateDoc || !empty($request->due_dateDoc_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Due Date';
                $history->previous = $lastDocument->due_dateDoc;
                $history->current = $document->due_dateDoc;
                $history->comment = implode($request->due_dateDoc_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->sop_type != $document->sop_type || !empty($request->sop_type_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'SOP Type';
                $history->previous = $lastDocument->sop_type;
                $history->current = $document->sop_type;
                $history->comment = $request->sop_type_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->reference_record != $document->reference_record || !empty($request->reference_record_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Reference Record';
                $history->previous = $lastDocument->reference_record;
                $history->current = $document->reference_record;
                $history->comment = $request->reference_record_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->notify_to != $document->notify_to || !empty($request->notify_to_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Notify To';
                $history->previous = $lastDocument->notify_to;
                $history->current = $document->notify_to;
                $history->comment = $request->notify_to_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->description != $document->description || !empty($request->description_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Description';
                $history->previous = $lastDocument->description;
                $history->current = $document->description;
                $history->comment = $request->description_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }


            if ($lastDocument->department_id != $document->department_id || !empty($request->department_id_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Department';
                $history->previous = Department::where('id', $lastDocument->department_id)->value('name');
                $history->current = Department::where('id', $document->department_id)->value('name');
                $history->comment = $request->department_id_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->document_type_id != $document->document_type_id || !empty($request->document_type_id_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Document';
                $history->previous = DocumentType::where('id', $lastDocument->document_type_id)->value('name');
                $history->current = DocumentType::where('id', $document->document_type_id)->value('name');
                $history->comment = implode($request->document_type_id_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            // if ($lastDocument->document_subtype_id != $document->document_subtype_id || ! empty($request->document_type_id_comment)) {
            //     $history = new DocumentHistory;
            //     $history->document_id = $id;
            //     $history->activity_type = 'Document Sub Type';
            //     $history->previous = DocumentType::where('id', $lastDocument->document_subtype_id)->value('name');
            //     $history->current = DocumentType::where('id', $document->document_subtype_id)->value('name');
            //     $history->comment = $request->document_subtype_id_comment;
            //     $history->user_id = Auth::user()->id;
            //     $history->user_name = Auth::user()->name;
            //     $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            //     $history->origin_state = $lastDocument->status;
            //     $history->save();
            // }
            // if ($lastDocument->document_language_id != $document->document_language_id || ! empty($request->document_language_id_comment)) {
            //     $history = new DocumentHistory;
            //     $history->document_id = $id;
            //     $history->activity_type = 'Document Language';
            //     $history->previous = DocumentLanguage::where('id', $lastDocument->document_language_id)->value('name');
            //     $history->current = DocumentLanguage::where('id', $document->document_language_id)->value('name');
            //     $history->comment = $request->document_language_id_comment;
            //     $history->user_id = Auth::user()->id;
            //     $history->user_name = Auth::user()->name;
            //     $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            //     $history->origin_state = $lastDocument->status;
            //     $history->save();
            // }
            if ($lastDocument->effective_date != $document->effective_date || !empty($request->effective_date_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Effective Date';
                $history->previous = $lastDocument->effective_date;
                $history->current = $document->effective_date;
                $history->comment = implode($request->effective_date_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->next_review_date != $document->next_review_date || !empty($request->next_review_date_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Next-Review Date';
                $history->previous = $lastDocument->next_review_date;
                $history->current = $document->next_review_date;
                $history->comment = implode($request->next_review_date_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->review_period != $document->review_period || !empty($request->review_period_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Review Period';
                $history->previous = $lastDocument->review_period;
                $history->current = $document->review_period;
                $history->comment = implode($request->review_period_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->revision_type != $document->revision_type || !empty($request->revision_type_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Revision Type';
                $history->previous = $lastDocument->revision_type;
                $history->current = $document->revision_type;
                $history->comment = $request->revision_type_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->major != $document->major || !empty($request->major_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Major';
                $history->previous = $lastDocument->major;
                $history->current = $document->major;
                $history->comment = $request->major_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->minor != $document->minor || !empty($request->minor_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Minor';
                $history->previous = $lastDocument->minor;
                $history->current = $document->minor;
                $history->comment = $request->minor_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->attach_draft_doocument != $document->attach_draft_doocument || !empty($request->attach_draft_doocument_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Draft Document';
                $history->previous = $lastDocument->attach_draft_doocument;
                $history->current = $document->attach_draft_doocument;
                $stage = $request->stage; // Assuming stage is being passed in the request

                if (is_array($request->attach_draft_doocument_comment)) {
                    if ($stage > 4 && $stage <= 10) {
                        // Custom handling for stages 5 to 11
                        $history->comment = implode(',', $request->attach_draft_doocument_comment);
                    } else {
                        // Normal handling for other stages
                        $history->comment = implode(',', $request->attach_draft_doocument_comment);
                    }
                } else {
                    // Handle the case where attach_draft_doocument_comment is not an array
                    $history->comment = $request->attach_draft_doocument_comment;
                }
                // $history->comment = implode($request->attach_draft_doocument_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            // add comment for drafter
            if ($lastDocument->drafters != $document->drafters || !empty($request->drafters_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Drafter';
                $history->previous = $lastDocument->drafters;
                $history->current = $document->drafters;
                $history->comment = implode($request->drafters_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastDocument->drafter_remarks != $document->drafter_remarks || !empty($request->drafter_remarks_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Drafter Remarks';
                $history->previous = $lastDocument->drafter_remarks;
                $history->current = $document->drafter_remarks;
                $history->comment = $request->drafter_remarks_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }


            if ($lastDocument->approver_remarks != $document->approver_remarks || !empty($request->approver_remarks_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Approver Remarks';
                $history->previous = $lastDocument->approver_remarks;
                $history->current = $document->approver_remarks;
                $history->comment = $request->approver_remarks_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastDocument->qa != $document->qa || !empty($request->qa_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = "QA's";
                $history->previous = $lastDocument->qa;
                $history->current = $document->qa;
                $history->comment = implode($request->qa_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastDocument->attach_effective_docuement != $document->attach_effective_docuement || !empty($request->attach_effective_docuement_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Effective Document';
                $history->previous = $lastDocument->attach_effective_docuement;
                $history->current = $document->attach_effective_docuement;
                $stage = $request->stage; // Assuming stage is being passed in the request

                if (is_array($request->attach_effective_docuement_comment)) {
                    if ($stage > 4 && $stage <= 10) {
                        // Custom handling for stages 5 to 11
                        $history->comment = implode(',', $request->attach_effective_docuement_comment);
                    } else {
                        // Normal handling for other stages
                        $history->comment = implode(',', $request->attach_effective_docuement_comment);
                    }
                } else {
                    // Handle the case where attach_effective_docuement_comment is not an array
                    $history->comment = $request->attach_effective_docuement_comment;
                }

                // $history->comment = implode($request->attach_effective_docuement_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            // QA and Hod 
            if ($lastDocument->hods != $document->hods || !empty($request->hods_comment)) {
                // dd($request->hods_comment);
                $history = new DocumentHistory;

                $history->document_id = $id;
                $history->activity_type = "HOD's";
                $temp = explode(',', $lastDocument->hods);
                $revew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRe = User::where('id', $temp[$i])->value('name');
                    array_push($revew, $dataRe);
                }
                $temped = explode(',', $document->hods);
                $revewnew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRenew = User::where('id', $temped[$i])->value('name');
                    array_push($revewnew, $dataRenew);
                }

                $history->previous = implode(',', $revew);
                $history->current = implode(',', $revewnew);
                $history->comment = implode($request->hods_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }


            if ($lastDocument->reviewers != $document->reviewers || !empty($request->reviewers_comment)) {

                $history = new DocumentHistory;

                $history->document_id = $id;
                $history->activity_type = 'Reviewers';
                $temp = explode(',', $lastDocument->reviewers);
                $revew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRe = User::where('id', $temp[$i])->value('name');
                    array_push($revew, $dataRe);
                }
                $temped = explode(',', $document->reviewers);
                $revewnew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRenew = User::where('id', $temped[$i])->value('name');
                    array_push($revewnew, $dataRenew);
                }

                $history->previous = implode(',', $revew);
                $history->current = implode(',', $revewnew);
                $history->comment = implode($request->reviewers_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastDocument->approvers != $document->approvers || !empty($request->approvers_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Approvers';
                $temp = explode(',', $lastDocument->approvers);
                $revew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRe = User::where('id', $temp[$i])->value('name');
                    array_push($revew, $dataRe);
                }
                $temped = explode(',', $document->approvers);
                $revewnew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRenew = User::where('id', $temped[$i])->value('name');
                    array_push($revewnew, $dataRenew);
                }

                $history->previous = implode(',', $revew);
                $history->current = implode(',', $revewnew);
                $history->comment = implode($request->approvers_comment);
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastDocument->reviewers_group != $document->reviewers_group || !empty($request->reviewers_group_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Reviewers Group';
                $temp = explode(',', $lastDocument->reviewers_group);
                $revew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRe = Grouppermission::where('id', $temp[$i])->value('name');
                    array_push($revew, $dataRe);
                }
                $temped = explode(',', $document->reviewers_group);
                $revewnew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRenew = Grouppermission::where('id', $temped[$i])->value('name');
                    array_push($revewnew, $dataRenew);
                }

                $history->previous = implode(',', $revew);
                $history->current = implode(',', $revewnew);
                $history->comment = $request->reviewers_group_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastDocument->approver_group != $document->approver_group || !empty($request->approver_group_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Approver Group';
                $temp = explode(',', $lastDocument->approver_group);
                $revew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRe = Grouppermission::where('id', $temp[$i])->value('name');
                    array_push($revew, $dataRe);
                }
                $temped = explode(',', $document->approver_group);
                $revewnew = [];
                for ($i = 0; $i < count($temp); $i++) {
                    $dataRenew = Grouppermission::where('id', $temped[$i])->value('name');
                    array_push($revewnew, $dataRenew);
                }

                $history->previous = implode(',', $revew);
                $history->current = implode(',', $revewnew);
                $history->comment = $request->approver_group_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastDocument->revision_summary != $document->revision_summary || !empty($request->revision_summary_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Revision Summery';
                $history->previous = $lastDocument->revision_summary;
                $history->current = $document->revision_summary;
                $history->comment = $request->revision_summary_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            $annexure = Annexure::firstOrNew([
                'document_id' => $id
            ]);

            if (!empty($request->serial_number)) {
                $annexure->sno = serialize($request->serial_number);
            }
            if (!empty($request->annexure_number)) {
                $annexure->annexure_no = serialize($request->annexure_number);
            }

            if (!empty($request->annexure_data)) {
                $annexure->annexure_title = serialize($request->annexure_data);
            }
            $annexure->save();

            $documentcontet = DocumentContent::firstOrNew([
                'document_id' => $id
            ]);
            $documentcontet->purpose = $request->purpose;
            $documentcontet->scope = $request->scope;
            $documentcontet->procedure = $request->procedure;
            $documentcontet->safety_precautions = $request->safety_precautions;

            // $documentcontet->short_description = $request->short_description ? serialize($request->short_description) : serialize([]);

            $documentcontet->responsibility = $request->responsibility ? serialize($request->responsibility) : serialize([]);
            $documentcontet->accountability = $request->accountability ? serialize($request->accountability) : serialize([]);
            $documentcontet->abbreviation = $request->abbreviation ? serialize($request->abbreviation) : serialize([]);
            $documentcontet->defination = $request->defination ? serialize($request->defination) : serialize([]);
            $documentcontet->reporting = $request->reporting ? serialize($request->reporting) : serialize([]);
            $documentcontet->materials_and_equipments = $request->materials_and_equipments ? serialize($request->materials_and_equipments) : serialize([]);
            $documentcontet->references = $request->references ? serialize($request->references) : serialize([]);
            $documentcontet->ann = $request->ann ? serialize($request->ann) : serialize([]);

            $documentcontet->hod_comments = $request->hod_comments;

            // $files = $request->has('existing_hod_attachments') && is_array($request->existing_hod_attachments) ? array_keys($request->existing_hod_attachments) : [];

            // if ($request->has('hod_attachments') && $request->hasFile('hod_attachments')) {
            //     foreach ($request->file('hod_attachments') as $file) {
            //         $name = 'hod_attachments-' . rand(1, 100) . '-' . time() . '.' . $file->getClientOriginalExtension();
            //         $file->move('upload/', $name);
            //         $files[] = $name;
            //     }
            // }

            // $documentcontet->hod_attachments = json_encode($files);

            if (!empty($request->existing_hod_attachments)) {
                $files = [];
                if ($request->hasfile('existing_hod_attachments')) {
                    foreach ($request->file('existing_hod_attachments') as $file) {
                        $name = $request->name . 'existing_hod_attachments' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                        $file->move('upload/', $name);
                        $files[] = $name;
                    }
                }
                $document->existing_hod_attachments = json_encode($files);
            }


            if (!empty($request->ann)) {
                $documentcontet->ann = serialize($request->ann);
            }

            if (!empty($request->annexuredata)) {
                $documentcontet->annexuredata = serialize($request->annexuredata);
            }
            if (!empty($request->distribution)) {
                $documentcontet->distribution = serialize($request->distribution);
            }

            foreach ($request->annexures as $annId => $content)
            {
                $ann = DocumentAnnexure::find($annId);
                if ($ann) {
                    $ann->content = $content;
                    $ann->save();
                }
            }


            $documentcontet->save();
            $annexure_data = $request->input('annexuredata');
                    $document->doc_content->annexuredata = serialize($annexure_data);
                    $document->doc_content->save();        

            if ($lastContent->purpose != $documentcontet->purpose || ! empty($request->purpose_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Purpose';
                $history->previous = $lastContent->purpose;
                $history->current = $documentcontet->purpose;
                $history->comment = $request->purpose_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->scope != $documentcontet->scope || !empty($request->scope_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Scope';
                $history->previous = $lastContent->scope;
                $history->current = $documentcontet->scope;
                $history->comment = $request->scope_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->responsibility != $documentcontet->responsibility || !empty($request->responsibility_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Responsibility';
                $history->previous = $lastContent->responsibility;
                $history->current = $documentcontet->responsibility;
                $history->comment = $request->responsibility_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->abbreviation != $documentcontet->abbreviation || !empty($request->abbreviation_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Abbreviation';
                $history->previous = $lastContent->abbreviation;
                $history->current = $documentcontet->abbreviation;
                $history->comment = $request->abbreviation_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->defination != $documentcontet->defination || !empty($request->defination_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Defination';
                $history->previous = $lastContent->defination;
                $history->current = $documentcontet->defination;
                $history->comment = $request->defination_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->materials_and_equipments != $documentcontet->materials_and_equipments || !empty($request->materials_and_equipments_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Materials and Equipments';
                $history->previous = $lastContent->materials_and_equipments;
                $history->current = $documentcontet->materials_and_equipments;
                $history->comment = $request->materials_and_equipments_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastContent->procedure != $documentcontet->procedure || !empty($request->procedure_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Procedure';
                $history->previous = $lastContent->procedure;
                $history->current = $documentcontet->procedure;
                $history->comment = $request->procedure_comment;

                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->reporting != $documentcontet->reporting || !empty($request->reporting_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Reporting';
                $history->previous = $lastContent->reporting;
                $history->current = $documentcontet->reporting;
                $history->comment = $request->reporting_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->references != $documentcontet->references || !empty($request->references_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'References';
                $history->previous = $lastContent->references;
                $history->current = $documentcontet->references;
                $history->comment = $request->references_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            if ($lastContent->ann != $documentcontet->ann || !empty($request->ann_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Annexure';
                $history->previous = $lastContent->ann;
                $history->current = $documentcontet->ann;
                $history->comment = $request->ann_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }
            if ($lastContent->distribution != $documentcontet->distribution || !empty($request->distribution_comment)) {
                $history = new DocumentHistory;
                $history->document_id = $id;
                $history->activity_type = 'Distribution';
                $history->previous = $lastContent->distribution;
                $history->current = $documentcontet->distribution;
                $history->comment = $request->distribution_comment;
                $history->user_id = Auth::user()->id;
                $history->user_name = Auth::user()->name;
                $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                $history->origin_state = $lastDocument->status;
                $history->save();
            }

            toastr()->success('Document Updated');
            // return redirect()->back();


            if (Helpers::checkRoles(3)) {
                return redirect('doc-details/' . $id);
            } else {
                return redirect('rev-details/' . $id);
            }
        } else {
            toastr()->error('Not working');

            return redirect()->back();
        }

        toastr()->success('Document Updated');

        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);
        $document->delete();
        toastr()->success('Deleted successfully');

        return redirect()->back();
    }

    public function createPDF($id)
    {
        $roles = explode(',', Auth::user()->role);
        $controls = PrintControl::whereIn('role_id', $roles)->first();

        if ($controls) {
            set_time_limit(30);
            $document = Document::find($id);
            $data = Document::find($id);
            $data->department = Department::find($data->department_id);
            $data['originator'] = User::where('id', $data->originator_id)->value('name');
            $time = Carbon::now();
            $data['originator_email'] = User::where('id', $data->originator_id)->value('email');
            $data['document_type_name'] = DocumentType::where('id', $data->document_type_id)->value('name');
            $data['document_type_code'] = DocumentType::where('id', $data->document_type_id)->value('typecode');
            $data['document_division'] = Division::where('id', $data->division_id)->value('name');
            $data['document_content'] = DocumentContent::where('document_id', $id)->first();
            $data['year'] = Carbon::parse($data->created_at)->format('Y');
            // $document = Document::where('id', $id)->get();
            // $pdf = PDF::loadView('frontend.documents.pdfpage', compact('data'))->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadview('frontend.documents.pdfpage', compact('data', 'time', 'document'))
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

            if ($controls->daily != 0) {
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->where('date', Carbon::now()->format('d-m-Y'))->count();
                if ($user + 1 <= $controls->daily) {
                    //Downlad History
                    $download = new DownloadHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->save();

                    // download PDF file with download method

                    return $pdf->download('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your daily download limit.');

                    return back();
                }
            } elseif ($controls->weekly != 0) {
                $weekDate = Carbon::now()->subDays(7)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->weekly) {
                    //Downlad History
                    $download = new DownloadHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->save();

                    // download PDF file with download method

                    return $pdf->download('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your weekly download limit.');

                    return back();
                }
            } elseif ($controls->monthly != 0) {
                $weekDate = Carbon::now()->subDays(30)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->monthly) {
                    //Downlad History
                    $download = new DownloadHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->save();

                    // download PDF file with download method

                    return $pdf->download('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your monthly download limit.');

                    return back();
                }
            } elseif ($controls->quatarly != 0) {
                $weekDate = Carbon::now()->subDays(90)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->quatarly) {
                    //Downlad History
                    $download = new DownloadHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->save();

                    // download PDF file with download method

                    return $pdf->download('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your quaterly download limit.');

                    return back();
                }
            } elseif ($controls->yearly != 0) {
                $weekDate = Carbon::now()->subDays(365)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->yearly) {
                    //Downlad History
                    $download = new DownloadHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->save();

                    // download PDF file with download method

                    return $pdf->download('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your yearly download limit.');

                    return back();
                }
            } else {
                toastr()->error('There is no controls provide for your role.');

                return back();
            }
        } else {
            toastr()->error('There is no controls provide for your role.');

            return back();
        }
    }

    public function viewPdf($id)
    {
        $depaArr = ['ACC' => 'Accounting', 'ACC3' => 'Accounting',];
        $data = Document::find($id);
        //$data->department = Department::find($data->department_id);
        $department = Department::find(Auth::user()->departmentid);
        $document = Document::find($id);

        if ($department) {
            $data['department_name'] = $department->name;
        } else {
            $data['department_name'] = '';
        }
        $data->department = $department;

        $data['originator'] = User::where('id', $data->originator_id)->value('name');
        $data['originator_email'] = User::where('id', $data->originator_id)->value('email');
        $data['document_type_name'] = DocumentType::where('id', $data->document_type_id)->value('name');
        $data['document_type_code'] = DocumentType::where('id', $data->document_type_id)->value('typecode');

        $data['document_division'] = Division::where('id', $data->division_id)->value('name');
        $data['year'] = Carbon::parse($data->created_at)->format('Y');
        $data['document_content'] = DocumentContent::where('document_id', $id)->first();

        // pdf related work
        $pdf = App::make('dompdf.wrapper');
        $time = Carbon::now();

        // return view('frontend.documents.pdfpage', compact('data', 'time', 'document'))->render();
        // $pdf = PDF::loadview('frontend.documents.new-pdf', compact('data', 'time', 'document'))

        $pdf = PDF::loadview('frontend.documents.pdfpage', compact('data', 'time', 'document'))
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
            ]);

        $pdf->setPaper('A4');
        $pdf->render();
        $canvas = $pdf->getDomPDF()->getCanvas();
        $canvas->set_default_view('FitB');
        $height = $canvas->get_height();
        $width = $canvas->get_width();

        $canvas->page_script('$pdf->set_opacity(0.1,"Multiply");');

        $canvas->page_text(
            $width / 4,
            $height / 2,
            Helpers::getDocStatusByStage($data->stage),
            null,
            25,
            [0, 0, 0],
            2,
            6,
            -20
        );

        if ($data->documents) {

            $pdfArray = explode(',', $data->documents);
            foreach ($pdfArray as $pdfFile) {
                $existingPdfPath = public_path('upload/PDF/' . $pdfFile);
                $permissions = 0644; // Example permission value, change it according to your needs
                if (file_exists($existingPdfPath)) {
                    // Create a new Dompdf instance
                    $options = new Options();
                    $options->set('chroot', public_path());
                    $options->set('isPhpEnabled', true);
                    $options->set('isRemoteEnabled', true);
                    $options->set('isHtml5ParserEnabled', true);
                    $options->set('allowedFileExtensions', ['pdf']); // Allow PDF file extension

                    $dompdf = new Dompdf($options);

                    chmod($existingPdfPath, $permissions);

                    // Load the existing PDF file
                    $dompdf->loadHtmlFile($existingPdfPath);

                    // Render the PDF
                    $dompdf->render();

                    // Output the PDF to the browser
                    $dompdf->stream();
                }
            }
        }

        $directoryPath = public_path("user/pdf/doc");
        $filePath = $directoryPath . '/SOP' . $id . '.pdf';

        if (!File::isDirectory($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true); // Recursive creation with read/write permissions
        }

        $pdf->save($filePath);

        return $pdf->stream('SOP' . $id . '.pdf');
    }

    public function printPDF($id)
    {

        $issue_copies = request('issue_copies');
        $print_reason = request('print_reason');
        $document_print_by = request('user_id');
        $documentNo = request('document_number');
        $NoofCopies = request('document_printed_copies');
        $IssueDate = request('date');
        $IssuanceTo = request('issuance_to');
        $IssuedCopies = request('issued_copies');
        $reasonIssue = request('issued_reason');
        $depart = request('department');
        $date = request('date');


        // $document_print_by = request('user_id');
        // $documentNo = request('document_number');
        // $NoofCopies = request('document_printed_copies');
        // $IssueDate = request('date');
        // $IssuanceTo = request('issuance_to');
        // $IssuedCopies = request('issued_copies');
        // $reasonIssue = request('issued_reason');
        // $depart = request('department');
        // $date = request('date');


        if (intval($issue_copies) < 1) {
            return "Cannot issue less than 1 copies! Requested $issue_copies no. of copies.";
        }
        $new = Document::find($id);
        $addNew = $new->id;

        $ModalData = new PrintHistory;
        $ModalData->issue_copies = $issue_copies;
        $ModalData->print_reason = $print_reason;
        $ModalData->user_id = $document_print_by;
        $ModalData->document_id = $addNew;
        $ModalData->document_number = $documentNo;
        $ModalData->document_printed_copies = $NoofCopies;
        $ModalData->date = $IssueDate;
        $ModalData->issuance_to = $IssuanceTo;
        $ModalData->issued_copies = $IssuedCopies;
        $ModalData->issued_reason = $reasonIssue;
        $ModalData->department = $depart;
        $ModalData->save();

        $roles = Auth::user()->userRoles()->select('role_id')->distinct()->pluck('role_id')->toArray();
        $controls = PrintControl::whereIn('role_id', $roles)->first();


        if ($controls) {
            set_time_limit(250);

            $document = Document::find($id);
            $data = Document::find($id);
            $data->department = Department::find($data->department_id);
            $data['originator'] = User::where('id', $data->originator_id)->value('name');
            $data['originator_email'] = User::where('id', $data->originator_id)->value('email');
            $data['document_content'] = DocumentContent::where('document_id', $id)->first();
            $data['document_type_name'] = DocumentType::where('id', $data->document_type_id)->value('name');
            $data['document_type_code'] = DocumentType::where('id', $data->document_type_id)->value('typecode');
            $data['document_division'] = Division::where('id', $data->division_id)->value('name');
            $data['issue_copies'] = $issue_copies;

            $data['year'] = Carbon::parse($data->created_at)->format('Y');
            // $document = Document::where('id', $id)->get();
            // $pdf = PDF::loadView('frontend.documents.pdfpage', compact('data'))->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            $pdf = App::make('dompdf.wrapper');
            $time = Carbon::now();


            $pdf = PDF::loadview('frontend.documents.pdfpage', compact('data', 'time', 'document', 'issue_copies', 'print_reason'))
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                ]);

            $pdf->setPaper('A4');
            $pdf->render();
            $canvas = $pdf->getDomPDF()->getCanvas();
            $canvas2 = $pdf->getDomPDF()->getCanvas();
            $height = $canvas->get_height();
            $width = $canvas->get_width();


            $canvas2->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($issue_copies, $canvas2) {
                // $page_switch_at = floor($pageCount/$issue_copies);

                $current_copy = round($pageNumber / $issue_copies) < 1 ? 1 : ceil($pageNumber / $issue_copies);
                $current_copy = $current_copy > $issue_copies ? $issue_copies : $current_copy;
                $text = "Issued Copy $current_copy of $issue_copies";
                $pageWidth = $canvas->get_width();
                $pageHeight = $canvas->get_height();
                $size = 10;
                $width = $fontMetrics->getTextWidth($text, null, $size);
                $canvas2->text($pageWidth - $width - 50, $pageHeight - 30, $text, null, $size);
            });

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


            if ($controls->daily != 0) {
                $user = PrintHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->where('date', Carbon::now()->format('d-m-Y'))->count();
                if ($user + 1 <= $controls->daily) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();


                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your daily print limit.');

                    return back();
                }
            } elseif ($controls->weekly != 0) {
                $weekDate = Carbon::now()->subDays(7)->format('d-m-Y');
                $user = PrintHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->weekly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();

                    // download PDF file with download method
                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your weekly print limit.');

                    return back();
                }
            } elseif ($controls->monthly != 0) {
                $weekDate = Carbon::now()->subDays(30)->format('d-m-Y');
                $user = PrintHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->monthly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();

                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your monthly print limit.');

                    return back();
                }
            } elseif ($controls->quatarly != 0) {
                $weekDate = Carbon::now()->subDays(90)->format('d-m-Y');
                $user = PrintHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->quatarly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();

                    // download PDF file with download method 

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your quaterly print limit.');

                    return back();
                }
            } elseif ($controls->yearly != 0) {
                $weekDate = Carbon::now()->subDays(365)->format('d-m-Y');
                $user = PrintHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->yearly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;

                    $download->save();

                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your yearly print limit.');

                    return back();
                }
            } else {
                toastr()->error('There is no controls provide for your role.');

                return back();
            }
        } else {
            toastr()->error('There is no controls provide for your role.');

            return back();
        }
    }

    public function printAnnexure($documentId, $annexure_number)
    {
        try {
            $document = Document::findOrFail($documentId);

            if ($document->doc_content && !empty($document->doc_content->annexuredata)) {
                $annexure_data = unserialize($document->doc_content->annexuredata);

                $annexure_data = $annexure_data[$annexure_number - 1];

            $document = Document::find($documentId);
            $data = Document::find($documentId);
            $data->department = Department::find($data->department_id);
            $data['originator'] = User::where('id', $data->originator_id)->value('name');
            $data['originator_email'] = User::where('id', $data->originator_id)->value('email');
            $data['document_content'] = DocumentContent::where('document_id', $documentId)->first();
            $data['document_type_name'] = DocumentType::where('id', $data->document_type_id)->value('name');
            $data['document_type_code'] = DocumentType::where('id', $data->document_type_id)->value('typecode');
            $data['document_division'] = Division::where('id', $data->division_id)->value('name');
            $data['year'] = Carbon::parse($data->created_at)->format('Y');
            $pdf = App::make('dompdf.wrapper');
            $time = Carbon::now();
            $pdf = PDF::loadview('frontend.documents.reports.annexure_report', compact('data', 'time', 'document', 'annexure_number', 'annexure_data'))
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

            return $pdf->stream('SOP'.$documentId.'.pdf');
            
        } else {
            throw new \Exception('Annexure Data Not Found');
        }

    } catch(\Exception $e) {
        return $e->getMessage();
    }

}

public function setReadonly($documentId, $annexure_number)
{
    try {
        $document = Document::findOrFail($documentId);

        if ($document->doc_content && !empty($document->doc_content->annexuredata)) {
            $annexure_data = unserialize($document->doc_content->annexuredata);

            // Ensure the annexure exists and is properly formatted
            if (isset($annexure_data[$annexure_number - 1])) {
                // If it's not an array, convert it into an array with content and readonly fields
                if (!is_array($annexure_data[$annexure_number - 1])) {
                    $annexure_data[$annexure_number - 1] = [
                        'content' => $annexure_data[$annexure_number - 1],
                        'readonly' => true,
                        'name' => 'Annexure A-' . $annexure_number . ' (Obsolete)'
                    ];
                } else {
                    // If it's already an array, set the readonly attribute and retain existing content
                    $annexure_data[$annexure_number - 1]['readonly'] = true;
                    $annexure_data[$annexure_number - 1]['name'] = 'Annexure A-' . $annexure_number . ' (Obsolete)';
                }

                // Save the updated annexure data back to the document
                $document->doc_content->annexuredata = serialize($annexure_data);
                $document->doc_content->save();

                return redirect()->back()->with('success', 'Annexure A-' . $annexure_number . ' set to readonly.');
            } else {
                throw new \Exception('Annexure not found.');
            }
        } else {
            throw new \Exception('Annexure Data Not Found');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

public function reviseAnnexure($documentId, $annexure_number)
{
    try {
        $document = Document::findOrFail($documentId);

        if ($document->doc_content && !empty($document->doc_content->annexuredata)) {
            $annexure_data = unserialize($document->doc_content->annexuredata);

            // Ensure the annexure exists and is properly formatted
            if (isset($annexure_data[$annexure_number - 1])) {
                // If it's not an array, convert it into an array with content and readonly fields
                if (!is_array($annexure_data[$annexure_number - 1])) {
                    $annexure_data[$annexure_number - 1] = [
                        'content' => $annexure_data[$annexure_number - 1],
                        'readonly' => true,
                        'sub_annexures' => []
                    ];
                } else {
                    // If it's already an array, set the readonly attribute and retain existing content
                    $annexure_data[$annexure_number - 1]['readonly'] = true;
                }

                // Generate a new sub-annexure
                $sub_annexure_count = count($annexure_data[$annexure_number - 1]['sub_annexures']);
                $annexure_data[$annexure_number - 1]['sub_annexures'][] = [
                    'content' => '',
                    'readonly' => false,
                    'name' => 'Sub-Annexure A' . $annexure_number . '.' . ($sub_annexure_count + 1) . ' (Revised)'
                ];

                // Save the updated annexure data back to the document
                $document->doc_content->annexuredata = serialize($annexure_data);
                $document->doc_content->save();

                return redirect()->back()->with('success', 'Annexure A-' . $annexure_number . ' set to readonly and new sub-annexure created.');
            } else {
                throw new \Exception('Annexure not found.');
            }
        } else {
            throw new \Exception('Annexure Data Not Found');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}



// 



// public function saveDocument(Request $request, $documentId)
// {
//     try {
//         $document = Document::findOrFail($documentId);

//         // Retrieve and serialize the annexure data
//         $annexure_data = $request->input('annexuredata');
//         $document->doc_content->annexuredata = serialize($annexure_data);
//         $document->doc_content->save();

//         return redirect()->back()->with('success', 'Document saved successfully.');
//     } catch (\Exception $e) {
//         return redirect()->back()->with('error', $e->getMessage());
//     }
// }





    public function import(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|mimes:xlsx,xls,csv,pdf'
        // ]);
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
            foreach ($uploadedFiles as $uploadedFile) {
                $extension = $uploadedFile->getClientOriginalExtension();
                if ($extension === 'pdf') {
                    // Process PDF file
                    $originalName = $uploadedFile->getClientOriginalName();
                    $destinationPath = public_path('upload/PDF');

                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    // $file = $request->file('file');
                    $storedFileName = $uploadedFile->storeAs('upload/PDF', $originalName);

                    toastr()->success('PDF file uploaded successfully!');
                } elseif ($extension === 'csv' || $extension === 'xls' || $extension === 'xlsx') {
                    $import = new DocumentsImport();
                    Excel::import($import, $uploadedFile);

                    toastr()->success('CSV file imported successfully!');
                } else {
                    toastr()->error('Invalid file format. Only PDF and CSV files are allowed.');
                }
            }

            return back();
        }

        toastr()->error('No files uploaded!');

        return back();
    }

    public function revision(Request $request, $id)
    {

        $document = Document::find($id);

        $revisionExists = Document::where([
            'document_type_id' => $document->document_type_id,
            'document_number' => $document->document_number,
            'major' => $request->major,
            'minor' => $request->minor
        ])->first();

        if ($revisionExists) {
            toastr()->error('Same version of document is already revised!!');
            return redirect()->back();
        } else {
            $document->revision = 'Yes';
            $document->revision_policy = $request->revision;
            $document->update();
            $newdoc = new Document();
            $newdoc->originator_id = $document->originator_id;
            $newdoc->division_id = $document->division_id;
            $newdoc->process_id = $document->process_id;
            $newdoc->revised = 'Yes';
            $newdoc->revised_doc = $document->id;
            $newdoc->document_name = $document->document_name;
            $newdoc->major = $request->major;
            $newdoc->minor = $request->minor;
            $newdoc->sop_type = $request->sop_type;
            $newdoc->short_description = $document->short_description;
            $newdoc->due_dateDoc = $document->due_dateDoc;
            $newdoc->description = $document->description;
            $newdoc->notify_to = json_encode($document->notify_to);
            $newdoc->reference_record = $document->reference_record;
            $newdoc->department_id = $document->department_id;
            $newdoc->document_type_id = $document->document_type_id;
            $newdoc->document_subtype_id = $document->document_subtype_id;
            $newdoc->document_language_id = $document->document_language_id;
            $newdoc->keywords = $document->keywords;
            $newdoc->effective_date = $document->effective_date;
            $newdoc->next_review_date = $document->next_review_date;
            $newdoc->review_period = $document->review_period;
            $newdoc->attach_draft_doocument = $document->attach_draft_doocument;
            $newdoc->attach_effective_docuement = $document->attach_effective_docuement;
            $newdoc->approvers = $document->approvers;
            $newdoc->reviewers = $document->reviewers;
            $newdoc->approver_group = $document->approver_group;
            $newdoc->reviewers_group = $document->reviewers_group;
            $newdoc->revision_summary = $document->revision_summary;
            $newdoc->training_required = $document->training_required;
            $newdoc->trainer = $request->trainer;
            $newdoc->hods = $document->hods;
            $newdoc->document_number = $document->document_number;
            $newdoc->comments = $request->comments;
            //$newdoc->purpose = $request->purpose;
            $newdoc->stage = 1;
            $newdoc->status = Stage::where('id', 1)->value('name');
            $newdoc->save();

            $doc_content = new DocumentContent();
            $doc_content->document_id = $newdoc->id;
            $doc_content->purpose = $doc_content->purpose;
            $doc_content->scope = $doc_content->scope;
            $doc_content->responsibility = $doc_content->responsibility;
            $doc_content->abbreviation = $doc_content->abbreviation;
            $doc_content->defination = $doc_content->defination;
            $doc_content->materials_and_equipments = $doc_content->materials_and_equipments;
            $doc_content->procedure = $doc_content->procedure;
            $doc_content->reporting = $doc_content->reporting;
            $doc_content->references = $doc_content->references;
            $doc_content->ann = $doc_content->ann;
            $doc_content->distribution = $doc_content->distribution;
            $doc_content->save();

            if ($document->training_required == 'yes') {
                $docTrain = DocumentTraining::where('document_id', $document->id)->first();
                if (!empty($docTrain)) {
                    $trainning = new DocumentTraining();
                    $trainning->document_id = $newdoc->id;
                    $trainning->trainer = $docTrain->trainer;
                    $trainning->cbt = $docTrain->cbt;
                    $trainning->type = $docTrain->type;
                    $trainning->comments = $docTrain->comments;
                    $trainning->save();
                }
            }

            $annexure = Annexure::where('document_id', $id)->first();
            $new_annexure = new Annexure();
            $new_annexure->document_id = $newdoc->id;
            $new_annexure->sno = $annexure->sno;
            $new_annexure->annexure_no = $annexure->annexure_no;
            $new_annexure->annexure_title = $annexure->annexure_title;
            $new_annexure->save();

            toastr()->success('Document is revised, you can change the body!!');
            return redirect()->route('documents.edit', $newdoc->id);
        }
    }

    public function revision_history($id)
    {
        try {
            $document = Document::find($id);
            $revised_document = Document::findOrFail($id);
            $parent_document = Document::findOrFail($revised_document->revised_doc);

            $revision_history = DocumentService::comapre_documents($parent_document, $revised_document);

            if ($revision_history['status'] == 'error') {
                throw new Exception($revision_history['message']);
            }


            return view('frontend.documents.revision_history', compact('revised_document', 'parent_document', 'document', 'revision_history'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function storePrintHistory(Request $request)
    {
        // Store print history data
        $print_history = new PrintPdfHistory();
        $print_history->document_name = $request->document_name;
        $print_history->issue_copies = $request->issue_copies;
        $print_history->print_reason = $request->print_reason;
        // $print_history->document_title = $request->document_title;
        $print_history->printed_by = Auth::user()->id;
        $print_history->printed_on = date('Y-m-d');

        $print_history->save();



        $documentToPdfMap = [
            'Analysis Protocol Template' => 'pdf/Analysis_Protocol_Template.pdf',
            'BPR Template' => 'pdf/BPR_Template.pdf',
            'CC Observations' => 'pdf/CC_Observations.pdf',
            'ECR Template' => 'pdf/ECR_Template.pdf',
            'Format Template 4' => 'pdf/Format_Template 4.pdf',
            'Format Template 3' => 'pdf/Format_Template-3.pdf',
            'Grid Backup CC' => 'pdf/grid_backu_CC.pdf',
            'Process Flow Chart Template' => 'pdf/Process_Flow_Chart_Template.pdf',
            'SDS Template' => 'pdf/SDS_Template.pdf',
            'SOP Template' => 'pdf/SOP_Template.pdf',
            'Specification TP Template' => 'pdf/Specification_TP_Template.pdf',
            'Specification Template' => 'pdf/Specification_Template.pdf',
        ];

        $pdfFilePath = public_path($documentToPdfMap[$request->document_name]);
        \Log::info('Checking file path: ' . $pdfFilePath);

        if (file_exists($pdfFilePath)) {
            return response()->file($pdfFilePath);
        } else {
            return response()->json(['status' => 'error', 'message' => 'File does not exist.']);
        }



        // $documentName = $request->document_name;
        // $pdfPath = $documentToPdfMap[$documentName] ?? null;

        // if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
        //     return response()->file(public_path($pdfPath));
        // }


    }


        public function revise_annexure($id)
        {
            $annexure = DocumentAnnexure::find($id);
            
            if ($annexure) {

                if (count($annexure->childs) > 0) {
                    $child = $annexure->childs()->latest()->first();
                }

                $revised_annexure = new DocumentAnnexure;
                $revised_annexure->document_id = $annexure->document_id;
                $revised_annexure->is_child = 1;
                $revised_annexure->parent_id = $annexure->id;
                $revised_annexure->content = isset($child) ? $child->content : $annexure->content;
                $revised_annexure->version = isset($child) ? number_format($child->version, 1) + 0.1 : number_format($annexure->version, 1) + 0.1;
                $revised_annexure->save();

                $annexure->is_revised = 1;
                $annexure->save();
            }

            // return redirect()->back();
            return redirect()->back()->with('open_tab', 'annexures');
        //    return view('documents.annexures', compact('document'));
        }

        public function obsolete_annexure($id)
        {
            $annexure = DocumentAnnexure::find($id);
            
            if ($annexure) {
                $annexure->is_obselete = 1;
                $annexure->save();

                $obsolete_annexure = new DocumentAnnexure;
                $obsolete_annexure->document_id = $annexure->document_id;
                $obsolete_annexure->is_child = 0;
                $obsolete_annexure->parent_id = $annexure->id;
                $obsolete_annexure->content = $annexure->content;
                
            }

            // return redirect()->back();
            return redirect()->back()->with('open_tab', 'annexures');
            // return redirect()->route('documents.show', [$annexure->document_id, 'tab' => 'annexures']);
        
        }
        

    // SOp pdf convert in word .docx file 
    public function downloadWord($id)
    {

        $document = Document::find($id);

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Fetch related data
        $department = $document->department;
        $originator = $document->originator;
        $documentType = $document->documentType;
        $division = $document->division;

        $data = [
            'department_name' => $department ? $department->name : '',
            'originator' => $originator ? $originator->name : '',
            'originator_email' => $originator ? $originator->email : '',
            'document_type_name' => $documentType ? $documentType->name : '',
            'document_type_code' => $documentType ? $documentType->typecode : '',
            'document_division' => $division ? $division->name : '',
            'year' => Carbon::parse($document->created_at)->format('Y'),
            'document_content' => DocumentContent::where('document_id', $id)->first(),
            'short_description' => Document::where('id', $id)->first(),
            'description' => Document::where('id', $id)->first(),
            'effective_date' => $document->effective_date,
            'next_review_date' => $document->next_review_date,
            'document_name' => $document->document_name,
            'stage' => $document->stage,
            'sop_type' => $document->sop_type ?? '',
            'revised' => $document->revised ?? 'No',
            'document_id' => $document->id ?? '',
            'document_number' => $document->document_number ?? '',
            'major' => $document->major ?? '',
            'minor' => $document->minor ?? '',
            'sop_type_short' => $document->sop_type_short ?? '',
            'department_id' => $document->department_id ?? '',
            'id' => $document->id ?? '',
            'division_id' => $document->division_id ?? '',
            'legacy_number' => $document->legacy_number ?? 'NA',
            'created_at' => $document->created_at,
            'hods' => $document->hods,
            'reviewers' => $document->reviewers,
            'approvers' => $document->approvers,
            'approver_group' => $document->approver_group,
        ];

        // Create a new PHPWord instance
        $phpWord = new PhpWord();

        // Add a section to the Word document
        $section = $phpWord->addSection();

        // Add Header with tables
        $header = $section->addHeader();

        // First Table in Header
        $headerTable = $header->addTable([
            'width' => 100 * 50,
            'borderSize' => 4,
            'borderColor' => '000000',
            'cellMargin' => 40
        ]);

        $headerTable->addRow(500);
        $headerTable->addCell(2000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addImage('https://navin.mydemosoftware.com/public/user/images/logo.png', [
            'width' => 90,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
        ]);

        $cell = $headerTable->addCell(6000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000']);
        $cell->addText(config('site.pdf_title'), ['size' => 14, 'bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell->addText($data['document_name'], ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $headerTable->addCell(2000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addImage('https://navin.mydemosoftware.com/public/user/images/logo.png', [
            'width' => 70,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
        ]);

        // Second Table in Header
        $headerTable = $header->addTable([
            'width' => 100 * 50,
            'borderSize' => 4,
            'borderColor' => '000000',
            'cellMargin' => 40
        ]);

        $headerTable->addRow(500);
        $headerTable->addCell(3000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addText($data['sop_type'], ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $cell = $headerTable->addCell(4000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000']);

        // Retrieve the type code from the database
        $temp = DB::table('document_types')->where('name', $data['document_type_name'])->value('typecode');

        if ($data['revised'] === 'Yes') {
            $sopNumber = Helpers::getDivisionName($data['division_id']) . '/'
                . ($data['document_type_name'] ? $temp . ' /' : '')
                . $data['year'] . '/000'
                . $data['document_'] . '/R'
                . $data['major'] . '.' . $data['minor'];
        } else {
            $sopNumber = Helpers::getDivisionName($data['division_id']) . '/'
                . ($data['document_type_name'] ? $temp . ' /' : '')
                . $data['year'] . '/000'
                . $data['document_id'] . '/R'
                . $data['major'] . '.' . $data['minor'];
        }

        $cell->addText($sopNumber, ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $headerTable->addCell(3000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addText(Helpers::getFullDepartmentName($data['department_id']), ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Third Table in Header
        $headerTable = $header->addTable([
            'width' => 100 * 50,
            'borderSize' => 4,
            'borderColor' => '000000',
            'cellMargin' => 40
        ]);

        // Address here 
        // $headerTable->addRow(500);
        // $headerTable->addCell(10000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addText('Address : ', ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Fourth Table in Header
        $headerTable = $header->addTable([
            'width' => 100 * 50,
            'borderSize' => 4,
            'borderColor' => '000000',
            'cellMargin' => 40
        ]);

        $headerTable->addRow(500);
        $headerTable->addCell(5000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addText('Effective Date: ' . Carbon::parse($data['effective_date'])->format('d-M-Y'), ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $headerTable->addCell(5000, ['valign' => 'center', 'borderSize' => 4, 'borderColor' => '000000'])->addText('Next Review Date: ' . Carbon::parse($data['next_review_date'])->format('d-M-Y'), ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);


        // Main Section start
        // Legacy Document Number
        $section->addTextBreak();
        // $section->addTextRun(['alignment' => 'right'])
        //     ->addText("Legacy Document Number: {$data['legacy_number']}", ['bold' => true]);

        // Objective
        $section->addTextBreak();
        $section->addText("1. Objective", ['bold' => true]);
        $section->addText($data['document_content']->purpose ?? '', ['align' => 'justify']);

        // Scope
        $section->addTextBreak();
        $section->addText("2. Scope", ['bold' => true]);
        $section->addText($data['document_content']->scope ?? '', ['align' => 'justify']);

        // Responsibility
        $section->addTextBreak();
        $section->addText("3. Responsibility", ['bold' => true]);
        $responsibility = $data['document_content']->responsibility ?? '[]';
        $responsibility = is_string($responsibility) ? unserialize($responsibility) : $responsibility;
        if (is_array($responsibility)) {
            $i = 1;
            foreach ($responsibility as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("3.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // Accountability
        $section->addTextBreak();
        $section->addText("4. Accountability", ['bold' => true]);
        $accountability = $data['document_content']->accountability ?? '[]';
        $accountability = is_string($accountability) ? unserialize($accountability) : $accountability;
        if (is_array($accountability)) {
            $i = 1;
            foreach ($accountability as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("4.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // References
        $section->addTextBreak();
        $section->addTextBreak();
        $section->addText("5. References", ['bold' => true]);
        $references = $data['document_content']->references ?? '[]';
        $references = is_string($references) ? unserialize($references) : $references;
        if (is_array($references)) {
            $i = 1;
            foreach ($references as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("5.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // Abbreviation
        $section->addTextBreak();
        $section->addTextBreak();
        $section->addText("6. Abbreviation", ['bold' => true]);
        $abbreviation = $data['document_content']->abbreviation ?? '[]';
        $abbreviation = is_string($abbreviation) ? unserialize($abbreviation) : $abbreviation;
        if (is_array($abbreviation)) {
            $i = 1;
            foreach ($abbreviation as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("6.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // Definitions
        $section->addTextBreak();
        $section->addText("7. Definitions", ['bold' => true]);
        $definitions = $data['document_content']->defination ?? '[]';
        $definitions = is_string($definitions) ? unserialize($definitions) : $definitions;
        if (is_array($definitions)) {
            $i = 1;
            foreach ($definitions as $key => $definition) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($definition)) {
                    $section->addText("7.{$prefix} " . $definition, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // General Instructions
        $section->addTextBreak();
        $section->addText("8. General Instructions", ['bold' => true]);
        $instructions = $data['document_content']->materials_and_equipments ?? '[]';
        $instructions = is_string($instructions) ? unserialize($instructions) : $instructions;
        if (is_array($instructions)) {
            $i = 1;
            foreach ($instructions as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("8.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // Procedure
        $section->addTextBreak();
        $section->addText("9. Procedure", ['bold' => true]);
        $procedure = $data['document_content']->procedure ?? '';
        $section->addText(strip_tags($procedure, '<br><table><th><td><tbody><tr><p><img><a><img><span><h1><h2><h3><h4><h5><h6><div><b><ol><li>'), ['align' => 'justify']);

        // Cross References
        $section->addTextBreak();
        $section->addText("10. Cross References", ['bold' => true]);
        $reporting = $data['document_content']->reporting ?? '[]';
        $reporting = is_string($reporting) ? unserialize($reporting) : $reporting;
        if (is_array($reporting)) {
            $i = 1;
            foreach ($reporting as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("10.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        // Annexure
        $section->addTextBreak();
        $section->addText("11. Annexure", ['bold' => true]);
        $annexure = $data['document_content']->ann ?? '[]';
        $annexure = is_string($annexure) ? unserialize($annexure) : $annexure;
        if (is_array($annexure)) {
            $i = 1;
            foreach ($annexure as $key => $res) {
                $isSub = str_contains($key, 'sub');
                $prefix = $isSub ? $i - 1 . '.' . ($sub_index ?? 1) : $i;
                if (!empty($res)) {
                    $section->addText("11.{$prefix} " . $res, ['align' => 'justify']);
                    $isSub ? $sub_index++ : $i++;
                    $sub_index = $isSub ? ($sub_index ?? 1) : 1;
                }
            }
        }

        //Document Control Information
        $last = DB::table('document_histories')
            ->where('document_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();
        $lastDate = $last ? Carbon::parse($last->created_at)->format('d-M-Y') : Carbon::parse($document->created_at)->format('d-M-Y');
        $section->addTextBreak();
        $section->addTextRun(['alignment' => 'center'])
            ->addText("Document Control Information", ['bold' => true]);

        // short_description
        $section->addTextBreak();
        $section->addText("Document Number", ['bold' => true]);
        $temp = DB::table('document_types')->where('name', $data['document_type_name'])->value('typecode');

        // Format the SOP number based on whether the document is revised or not
        if ($data['revised'] === 'Yes') {
            $sopNumber = Helpers::getDivisionName($data['division_id']) . '/'
                . ($data['document_type_name'] ? $temp . ' /' : '')
                . $data['year'] . '/000'
                . $data['document_id'] . '/R'
                . $data['major'] . '.' . $data['minor'];
        } else {
            $sopNumber = $data['sop_type_short'] . '/'
                . $data['department_id'] . '/000'
                . $data['document_id'] . '/R'
                . $data['major'] . '.' . $data['minor'];
        }

        // Add the formatted SOP number to the section text
        $section->addText(
            $sopNumber,
            ['size' => 12],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]
        );
        $section->addText("Title", ['bold' => true]);
        $section->addText($data['document_name'], ['alignment' => 'right']);
        // dd($data['document_name']);
        $section->addText("Short Description", ['bold' => true]);
        $section->addText($data['short_description']->short_description ?? '', ['alignment' => 'right']);

        // Scope
        // $section->addTextBreak();
        $section->addText("Description", ['bold' => true]);
        $section->addText($data['description']->description ?? '');

        $section->addText("Last Changed", ['bold' => true]);
        $section->addText($lastDate);


        $section->addText("Changed By", ['bold' => true]);
        $section->addText($data['originator']->originator ?? '');


        //  Signatute table Start

        $signatureOriginatorData = DB::table('stage_manages')
            ->where('document_id', $id)
            ->whereIn('stage', ['4', 'In-HOD Review', 'In-Approval'])
            ->latest()
            ->first();

        $signatureReviewerData = DB::table('stage_manages')
            ->where('document_id', $id)
            ->where('stage', 'Reviewed')
            ->get();

        $signatureApprovalData = DB::table('stage_manages')
            ->where('document_id', $id)
            ->where('stage', 'Approved')
            ->latest()
            ->first();

        // Add Originator Table
        $section->addText("Originator", ['bold' => true]);
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell(2000)->addText("Originator");
        $table->addCell(2000)->addText("Department");
        $table->addCell(2000)->addText("Status");
        $table->addCell(2000)->addText("E-Signature");
        $table->addCell(2000)->addText("Comments");

        $table->addRow();
        $table->addCell(2000)->addText($data['originator']);
        $table->addCell(2000)->addText($document->originator && $document->originator->department ? $document->originator->department->name : '');
        $table->addCell(2000)->addText("Initiation Completed");
        $table->addCell(2000)->addText($data['originator_email']);
        $table->addCell(2000)->addText($signatureOriginatorData && $signatureOriginatorData->comment ? $signatureOriginatorData->comment : '');

        // Add HOD Table
        $section->addTextBreak();
        $section->addText("HOD", ['bold' => true]);
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell(2000)->addText("HOD");
        $table->addCell(2000)->addText("Department");
        $table->addCell(2000)->addText("Status");
        $table->addCell(2000)->addText("E-Signature");
        $table->addCell(2000)->addText("Comments");

        if ($data['hods']) {
            $hods = explode(',', $data['hods']);
            foreach ($hods as $hod) {
                $user = DB::table('users')->where('id', $hod)->first();
                $dept = DB::table('departments')->where('id', $user->departmentid)->value('name');
                $date = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $hod)
                    ->where('stage', 'HOD Review Complete')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->first();
                $comment = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $hod)
                    ->where('stage', 'HOD Review Complete')
                    ->latest()
                    ->first();
                $reject = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $hod)
                    ->where('stage', 'Cancel-by-HOD')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->first();

                $table->addRow();
                $table->addCell(2000)->addText($user->name);
                $table->addCell(2000)->addText($dept);
                if ($date) {
                    $table->addCell(2000)->addText("HOD Review Complete");
                } elseif ($reject) {
                    $table->addCell(2000)->addText("HOD Rejected");
                } else {
                    $table->addCell(2000)->addText("HOD Review Pending");
                }
                $table->addCell(2000)->addText($user->email);
                $table->addCell(2000)->addText($comment ? $comment->comment : '');
            }
        }

        // Add Reviewers Table
        $section->addTextBreak();
        $section->addText("Reviews", ['bold' => true]);


        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell(2000)->addText("Reviewer");
        $table->addCell(2000)->addText("Department");
        $table->addCell(2000)->addText("Status");
        $table->addCell(2000)->addText("E-Signature");
        $table->addCell(2000)->addText("Comments");

        if ($data['reviewers']) {
            $reviewers = explode(',', $data['reviewers']);
            foreach ($reviewers as $reviewer) {
                $user = DB::table('users')->where('id', $reviewer)->first();
                $dept = DB::table('departments')->where('id', $user->departmentid)->value('name');
                $date = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $reviewer)
                    ->where('stage', 'Reviewed')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->first();
                $comment = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $reviewer)
                    ->where('stage', 'Reviewed')
                    ->latest()
                    ->first();
                $reject = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $reviewer)
                    ->where('stage', 'Cancel-by-Reviewer')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->first();

                $table->addRow();
                $table->addCell(2000)->addText($user->name);
                $table->addCell(2000)->addText($dept);
                if ($date) {
                    $table->addCell(2000)->addText("Review Completed");
                } elseif ($reject) {
                    $table->addCell(2000)->addText("Review Rejected");
                } else {
                    $table->addCell(2000)->addText("Review Pending");
                }
                $table->addCell(2000)->addText($user->email);
                $table->addCell(2000)->addText($comment ? $comment->comment : '');
            }
        }

        // Add Approvals Table
        $section->addTextBreak();
        $section->addTextBreak();
        $section->addText("Approvals", ['bold' => true]);
        $section->addTextBreak();
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell(2000)->addText("Approver");
        $table->addCell(2000)->addText("Department");
        $table->addCell(2000)->addText("Status");
        $table->addCell(2000)->addText("E-Signature");
        $table->addCell(2000)->addText("Comments");

        if ($data['approvers']) {
            $approvers = explode(',', $data['approvers']);
            foreach ($approvers as $approver) {
                $user = DB::table('users')->where('id', $approver)->first();
                $dept = DB::table('departments')->where('id', $user->departmentid)->value('name');
                $date = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $approver)
                    ->where('stage', 'Approved')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->first();
                $comment = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $approver)
                    ->where('stage', 'Approved')
                    ->latest()
                    ->first();
                $reject = DB::table('stage_manages')
                    ->where('document_id', $id)
                    ->where('user_id', $approver)
                    ->where('stage', 'Cancel-by-Approver')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->first();

                $table->addRow();
                $table->addCell(2000)->addText($user->name);
                $table->addCell(2000)->addText($dept);
                if ($date) {
                    $table->addCell(2000)->addText("Approval Completed");
                } elseif ($reject) {
                    $table->addCell(2000)->addText("Approval Rejected");
                } else {
                    $table->addCell(2000)->addText("Approval Pending");
                }
                $table->addCell(2000)->addText($user->email);
                $table->addCell(2000)->addText($comment ? $comment->comment : '');
            }
        }

        // Add Footer with tables
        $footer = $section->addFooter();

        // Footer Table
        $footerTable = $footer->addTable([
            'width' => 100 * 50,
            'borderSize' => 4,
            'borderColor' => '000000',
            'cellMargin' => 50
        ]);

        $footerTable->addRow();
        $cell = $footerTable->addCell(5000, ['valign' => 'center']);
        if ($cell) {
            $temp = DB::table('document_types')->where('name', $data['document_type_name'])->value('typecode');
            if ($data['revised'] === 'Yes') {
                $cell->addText(
                    Helpers::getDivisionName($data['document_id']) . '' . ($data['sop_type_short'] ? $temp : '') . '/' . $data['year'] . '/000' . $data['document_number'] . '/R' . $data['major'] . '.' . $data['minor'],
                    ['size' => 12],
                    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]
                );
            } else {
                $cell->addText(
                    $data['sop_type_short'] . '/' . $data['department_id'] . '000' . $data['id'] . '/R' . $data['major'] . '.' . $data['minor'],
                    ['size' => 12]
                );
            }
        }

        $footerTable->addCell(5000, ['valign' => 'center'])->addText('Printed On: ' . Carbon::now()->format('d-M-Y h:i A'), ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

        // Save the Word file
        $directoryPath = public_path("user/word/doc");
        $filePath = $directoryPath . '/SOP_' . $id . '.docx';

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        // Return response to download the file
        return response()->download($filePath)->deleteFileAfterSend(true);
    }


    public function printDownloadPDF($id)
    {

        $issue_copies = request('issue_copies');
        $print_reason = request('print_reason');
        $document_print_by = request('user_id');
        $IssueDate = request('date');
        $IssuedCopies = request('issued_copies');
        $date = request('date');


        if (intval($issue_copies) < 1) {
            return "Cannot issue less than 1 copies! Requested $issue_copies no. of copies.";
        }
        $new = Document::find($id);
        $addNew = $new->id;

        $ModalData = new DownloadHistory();
        $ModalData->issue_copies = $issue_copies;
        $ModalData->user_id = $document_print_by;
        $ModalData->document_id = $addNew;
        $ModalData->issued_copies = $IssuedCopies;
        $ModalData->save();

        // dd($ModalData = new DownloadHistory());

        $roles = Auth::user()->userRoles()->select('role_id')->distinct()->pluck('role_id')->toArray();
        $controls = PrintControl::whereIn('role_id', $roles)->first();


        if ($controls) {
            set_time_limit(250);

            $document = Document::find($id);
            $data = Document::find($id);
            $data->department = Department::find($data->department_id);
            $data['originator'] = User::where('id', $data->originator_id)->value('name');
            $data['originator_email'] = User::where('id', $data->originator_id)->value('email');
            $data['document_content'] = DocumentContent::where('document_id', $id)->first();
            $data['document_type_name'] = DocumentType::where('id', $data->document_type_id)->value('name');
            $data['document_type_code'] = DocumentType::where('id', $data->document_type_id)->value('typecode');
            $data['document_division'] = Division::where('id', $data->division_id)->value('name');
            $data['issue_copies'] = $issue_copies;

            $data['year'] = Carbon::parse($data->created_at)->format('Y');
            // $document = Document::where('id', $id)->get();
            // $pdf = PDF::loadView('frontend.documents.pdfpage', compact('data'))->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            $pdf = App::make('dompdf.wrapper');
            $time = Carbon::now();


            $pdf = PDF::loadview('frontend.documents.download_document', compact('data', 'time', 'document', 'issue_copies', 'print_reason'))
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                ]);

            $pdf->setPaper('A4');
            $pdf->render();
            $canvas = $pdf->getDomPDF()->getCanvas();
            $canvas2 = $pdf->getDomPDF()->getCanvas();
            $height = $canvas->get_height();
            $width = $canvas->get_width();


            $canvas2->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($issue_copies, $canvas2) {
                // $page_switch_at = floor($pageCount/$issue_copies);

                $current_copy = round($pageNumber / $issue_copies) < 1 ? 1 : ceil($pageNumber / $issue_copies);
                $current_copy = $current_copy > $issue_copies ? $issue_copies : $current_copy;
                $text = "Issued Copy $current_copy of $issue_copies";
                $pageWidth = $canvas->get_width();
                $pageHeight = $canvas->get_height();
                $size = 10;
                $width = $fontMetrics->getTextWidth($text, null, $size);
                $canvas2->text($pageWidth - $width - 50, $pageHeight - 30, $text, null, $size);
            });

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


            if ($controls->daily != 0) {
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->where('date', Carbon::now()->format('d-m-Y'))->count();
                if ($user + 1 <= $controls->daily) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();


                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your daily print limit.');

                    return back();
                }
            } elseif ($controls->weekly != 0) {
                $weekDate = Carbon::now()->subDays(7)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->weekly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();

                    // download PDF file with download method
                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your weekly print limit.');

                    return back();
                }
            } elseif ($controls->monthly != 0) {
                $weekDate = Carbon::now()->subDays(30)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->monthly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();

                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your monthly print limit.');

                    return back();
                }
            } elseif ($controls->quatarly != 0) {
                $weekDate = Carbon::now()->subDays(90)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->quatarly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;
                    $download->save();

                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your quaterly print limit.');

                    return back();
                }
            } elseif ($controls->yearly != 0) {
                $weekDate = Carbon::now()->subDays(365)->format('d-m-Y');
                $user = DownloadHistory::where('user_id', Auth::user()->id)->where('document_id', $id)->whereBetween('date', [$weekDate, Carbon::now()->format('d-m-Y')])->count();
                if ($user + 1 <= $controls->yearly) {
                    //Downlad History
                    $download = new PrintHistory;
                    $download->document_id = $id;
                    $download->user_id = Auth::user()->id;
                    $download->role_id = Auth::user()->role;
                    $download->date = Carbon::now()->format('d-m-Y');
                    $download->print_reason = $print_reason;
                    $download->issue_copies = $issue_copies;

                    $download->save();

                    // download PDF file with download method

                    return $pdf->stream('SOP' . $id . '.pdf');
                } else {
                    toastr()->error('You breach your yearly print limit.');

                    return back();
                }
            } else {
                toastr()->error('There is no controls provide for your role.');

                return back();
            }
        } else {
            toastr()->error('There is no controls provide for your role.');

            return back();
        }
    }
    public function delegate($id){
        $document = Document::find($id);
        $ids=$id;
        $hods = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 4)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        $qa = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 7)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();

        $drafter = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 40)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
            $reviewer = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 2)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        //$approvers = User::get();
        $approvers = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->select('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the select statement
            ->where('user_roles.q_m_s_processes_id', 24)
            ->where('user_roles.q_m_s_roles_id', 1)
            ->groupBy('user_roles.q_m_s_processes_id', 'users.id', 'users.role', 'users.name') // Include all selected columns in the group by clause
            ->get();
        return view('frontend.documents.comps.delegate_page', compact('ids', 'document','reviewer','approvers','hods','drafter','qa'));
    }
    public function delegateUpdate(Request $request) {
        // Check if document_id is passed in the request
        if (!$request->has('document_id') || empty($request->document_id)) {
            toastr()->error('Document ID is missing in the request.');
            return redirect()->back();
        }
    
        // Attempt to find the document with the provided ID
        $document = Document::find($request->document_id);
    
        // Check if the document exists in the database
        if (!$document) {
            toastr()->error('Document not found. Please check the Document ID.');
            return redirect()->back();
        }
    
        // Debug the request data if necessary
        // dd($request->all());
    
        // Update the document fields only if the request has the corresponding data
        if ($document->stage <= 5 && (!empty($request->reviewers) || $request->reviewers != $document->reviewers)) {
            $document->reviewers = implode(',', $request->reviewers);
        }
        if ($document->stage <= 6 && !empty($request->approvers)) {
            $document->approvers = implode(',', $request->approvers);
        }
        if ($document->stage <= 3 && empty($request->hods)) {
            $document->hods = implode(',', $request->hods);
        }
        if ($document->stage <= 4 && !empty($request->qa)) {
            $document->qa = implode(',', $request->qa);
        }
        if ($document->stage <= 2 && !empty($request->drafters)) {
            $document->drafters = implode(',', $request->drafters);
        }
    
        // Save the updated document
        $document->update();
    
        // toastr()->success('Document Updated by Delegate Person');
        return redirect()->back();
    }
}
