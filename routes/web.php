

<?php

use App\Http\Controllers\ActionItemController;
use App\Http\Controllers\Ajax\AjaxController;
use App\Http\Controllers\OpenStageController;
use App\Http\Controllers\rcms\InternalauditController;
use App\Http\Controllers\rcms\RootCauseController;
use App\Http\Controllers\TMSController;
use App\Http\Controllers\RiskManagementController;
use App\Http\Controllers\ChangeControlController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentDetailsController;
use App\Http\Controllers\PrintRequestController;
use App\Http\Controllers\rcms\DesktopController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\MytaskController;
use App\Http\Controllers\CabinateController;
use App\Http\Controllers\rcms\{CCController, DeviationController, IncidentController};
use App\Http\Controllers\rcms\EffectivenessCheckController;
use App\Http\Controllers\rcms\ObservationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentContentController;
use App\Http\Controllers\ErrataController;
use App\Http\Controllers\ExtensionNewController;
use App\Http\Controllers\ImportController;
// use App\Http\Controllers\tms\JobTrainingController;
use App\Http\Controllers\InductionTrainingcontroller;
use App\Http\Controllers\OOSMicroController;
use App\Http\Controllers\rcms\AuditeeController;
use App\Http\Controllers\rcms\NonConformaceController;
use App\Http\Controllers\rcms\CapaController;
use App\Http\Controllers\rcms\FailureInvestigationController;
use App\Http\Controllers\rcms\LabIncidentController;
use App\Http\Controllers\rcms\AuditProgramController;
use App\Http\Controllers\rcms\ExtensionController;
use App\Http\Controllers\rcms\ManagementReviewController;
use App\Http\Controllers\rcms\OOCController;
use App\Http\Controllers\rcms\OOSController;
use App\Http\Controllers\rcms\RcmsDashboardController;
use App\Http\Controllers\tms\EmployeeController;
// use App\Http\Controllers\tms\JobTrainingController;

use App\Http\Controllers\tms\QuestionBankController;
use App\Http\Controllers\tms\QuestionController;
use App\Http\Controllers\tms\QuizeController;
use App\Http\Controllers\rcms\OOTController;
use App\Http\Controllers\tms\TrainerController;
use App\Imports\DocumentsImport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\tms\JobTrainingController;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [UserLoginController::class, 'userlogin']);
Route::get('/login', [UserLoginController::class, 'userlogin'])->name('login');
Route::post('/logincheck', [UserLoginController::class, 'logincheck']);
Route::get('/logout', [UserLoginController::class, 'logout'])->name('logout');
Route::post('/rcms_check', [UserLoginController::class, 'rcmscheck']);
//Route::get('/', [UserLoginController::class, 'userlogin']);
Route::get('/error', function () {
    return view('error');
})->name('error.route');

//!---------------- starting login  ---------------------------//

Route::get('/', [UserLoginController::class, 'userlogin']);
Route::view('forgot-password', 'frontend.forgot-password');
// Route::view('dashboard', 'frontend.dashboard');


Route::get('data-fields', function () {
    return view('frontend.change-control.data-fields');
});
Route::middleware(['auth', 'prevent-back-history', 'user-activity'])->group(function () {
    Route::resource('change-control', OpenStageController::class);
    Route::get('change-control-audit/{id}', [OpenStageController::class, 'auditTrial']);
    Route::get('change-control-audit-detail/{id}', [OpenStageController::class, 'auditDetails']);
    Route::post('division/change/{id}', [OpenStageController::class, 'division'])->name('division_change');
    Route::get('send-notification/{id}', [OpenStageController::class, 'notification']);
    Route::resource('documents', DocumentController::class);
    Route::post('revision/{id}', [DocumentController::class, 'revision']);
    Route::get('delegate/{id}', [DocumentController::class, 'delegate']);
    Route::post('delegate-updated', [DocumentController::class, 'delegateUpdate']);
    Route::get('/documentExportPDF', [DocumentController::class, 'documentExportPDF'])->name('documentExportPDF');
    Route::get('/documentExportEXCEL', [DocumentController::class, 'documentExportEXCEL'])->name('documentExportEXCEL');
    Route::post('/import', [DocumentController::class, 'import'])->name('csv.import');
    Route::post('/importpdf', [ImportController::class, 'PDFimport']);
    Route::post('division_submit', [DocumentController::class, 'division'])->name('division_submit');
    //Route::post('set/division', [DocumentController::class, 'division'])->name('division_submit');
    Route::post('dcrDivision', [DocumentController::class, 'dcrDivision'])->name('dcrDivision_submit');
    Route::get('documents/generatePdf/{id}', [DocumentController::class, 'createPDF']);

    Route::get('documents/reviseCreate/{id}', [DocumentController::class, 'revise_create']);

    Route::get('documents/printPDF/{id}', [DocumentController::class, 'printPDF'])->name('document.print.pdf');
    Route::post('documents/pdfHistory/', [DocumentController::class, 'storePrintHistory'])->name('document.print.pdf.history');
    Route::get('documents/printAnnexure/{document}/{annexure}', [DocumentController::class, 'printAnnexure'])->name('document.print.annexure');
    Route::get('documents/viewpdf/{id}', [DocumentController::class, 'viewPdf']);
    Route::get('documents/revision-history/{id}', [DocumentController::class, 'revision_history'])->name('document.revision.history');
    Route::resource('documentsContent', DocumentContentController::class);
    Route::get('doc-details/{id}', [DocumentDetailsController::class, 'viewdetails']);
    Route::put('sendforstagechanage', [DocumentDetailsController::class, 'sendforstagechanage']);
    Route::put('sendfordraft', [DocumentDetailsController::class, 'sendfordraft']);
    Route::get('notification/{id}', [DocumentDetailsController::class, 'notification']);
    Route::get('/get-data', [DocumentDetailsController::class, 'getData'])->name('get.data');
    Route::post('/send-notification', [DocumentDetailsController::class, 'sendNotification']);
    Route::get('/search', [DocumentDetailsController::class, 'search']);
    Route::get('/advanceSearch', [DocumentDetailsController::class, 'searchAdvance']);
    Route::get('auditPrint/{id}', [DocumentDetailsController::class, 'printAudit']);
    Route::get('mytaskdata', [MytaskController::class, 'index']);
    Route::get('mydms', [CabinateController::class, 'index']);
    Route::get('email', [CabinateController::class, 'email']);
    Route::get('rev-details/{id}', [MytaskController::class, 'reviewdetails']);
    Route::post('send-change-control/{id}', [ChangeControlController::class, 'statechange']);
    Route::get('audit-trial/{id}', [DocumentDetailsController::class, 'auditTrial']);
    Route::get('audit-individual/{id}/{user}', [DocumentDetailsController::class, 'auditTrialIndividual']);
    Route::get('audit-detail/{id}', [DocumentDetailsController::class, 'auditDetails'])->name('audit-detail');
    Route::post('update-doc/{id}', [DocumentDetailsController::class, 'updatereviewers'])->name('update-doc');
    Route::get('audit-details/{id}', [DocumentDetailsController::class, 'getAuditDetail'])->name('audit-details');


    // pdf to word/////////////////////////////////////////////////////
    Route::get('/document/download-word/{id}', [DocumentController::class, 'downloadWord'])->name('download.word');

    Route::get('/document/download-print/{id}', [DocumentController::class, 'printDownloadPDF'])->name('document.print.downloadpdf');
    ////////////////////////////////////////////////////////////////

    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('analytics', [DashboardController::class, 'analytics']);
    Route::post('subscribe', [DashboardController::class, 'subscribe']);
    Route::resource('TMS', TMSController::class);
    Route::get('TMS-details/{id}/{sopId}', [TMSController::class, 'viewTraining']);
    Route::get('training/{id}/', [TMSController::class, 'training']);
    Route::get('trainingQuestion/{id}/', [TMSController::class, 'trainingQuestion']);
    Route::get('training-notification/{id}', [TMSController::class, 'notification']);
    Route::post('trainingComplete/{id}', [TMSController::class, 'trainingStatus']);
    Route::get('training-overall-status/{id}', [TMSController::class, 'trainingOverallStatus']);
    //Route::post('trainingSubmitData/{id}', [TMSController::class, 'trainingSubmitData']);
    Route::get('tms-audit/{id}', [TMSController::class, 'auditTrial']);
    Route::get('tms-audit-detail/{id}', [TMSController::class, 'auditDetails']);
    // Route::post('import', function () {
    //     Excel::import(new DocumentsImport, request()->file('file'));
    //     return redirect()->back()->with('success', 'Data Imported Successfully');
    // });
    Route::get('example/{id}/', [TMSController::class, 'example']);
    //Route::post('logs', [TMSController::class, 'logsTms_dashboard'])->name('logstms');
     Route::match(['get', 'post'],'logs', [TMSController::class, 'logsTms_dashboard'])->name('logstms');
    Route::get('/traneeLogsreport/{training_id}/{trainee_id}', [TMSController::class, 'logsReportTranee'])->name('traneeLogsreport');


   
    

    // Questions Part
    Route::resource('question', QuestionController::class);
    Route::get('questiondata/{id}', [QuestionBankController::class, 'datag'])->name('questiondata');
    Route::resource('question-bank', QuestionBankController::class);
    Route::resource('quize', QuizeController::class);
    Route::get('data/{id}', [QuizeController::class, 'datag'])->name('data');
    Route::get('datag/{id}', [QuizeController::class, 'data'])->name('datag');
    //-----------------------QMS----------------
    Route::get('qms-dashboard', [RcmsDashboardController::class, 'index']);
});

// ====================================Capa=======================
Route::get('capa', [CapaController::class, 'capa']);
Route::post('capastore', [CapaController::class, 'capastore'])->name('capastore');
Route::post('capaUpdate/{id}', [CapaController::class, 'capaUpdate'])->name('capaUpdate');
Route::get('capashow/{id}', [CapaController::class, 'capashow'])->name('capashow');
Route::post('capa/stage/{id}', [CapaController::class, 'capa_send_stage'])->name('capa_send_stage');
Route::post('capa/cancel/{id}', [CapaController::class, 'capaCancel'])->name('capaCancel');
Route::post('capa/reject/{id}', [CapaController::class, 'capa_reject'])->name('capa_reject');
Route::post('capa/Qa/{id}', [CapaController::class, 'capa_qa_more_info'])->name('capa_qa_more_info');
Route::get('CapaAuditTrial/{id}', [CapaController::class, 'CapaAuditTrial']);
Route::get('auditDetailsCapa/{id}', [CapaController::class, 'auditDetailsCapa'])->name('showCapaAuditDetails');
Route::post('capa_child/{id}', [CapaController::class, 'child_change_control'])->name('capa_child_changecontrol');
Route::post('effectiveness_check/{id}', [CapaController::class, 'effectiveness_check'])->name('capa_effectiveness_check');

// ==============================management review ==========================manage

Route::post('managestore', [ManagementReviewController::class, 'managestore'])->name('managestore');
Route::post('manageUpdate/{id}', [ManagementReviewController::class, 'manageUpdate'])->name('manageUpdate');
Route::get('manageshow/{id}', [ManagementReviewController::class, 'manageshow'])->name('manageshow');
Route::post('manage/stage/{id}', [ManagementReviewController::class, 'manage_send_stage'])->name('manage_send_stage');
Route::post('manage/cancel/{id}', [ManagementReviewController::class, 'manageCancel'])->name('manageCancel');
Route::post('manage/reject/{id}', [ManagementReviewController::class, 'manage_reject'])->name('manage_reject');
Route::post('manage/Qa/{id}', [ManagementReviewController::class, 'manage_qa_more_info'])->name('manage_qa_more_info');
Route::get('ManagementReviewAuditTrial/{id}', [ManagementReviewController::class, 'ManagementReviewAuditTrial']);
Route::get('ManagementReviewAuditDetails/{id}', [ManagementReviewController::class, 'ManagementReviewAuditDetails']);


/********************************************* Deviation Starts *******************************************/

Route::post('deviation_child/{id}', [DeviationController::class, 'deviation_child_1'])->name('deviation_child_1');

Route::get('DeviationAuditTrial/{id}', [DeviationController::class, 'DeviationAuditTrial']);
Route::post('DeviationAuditTrial/{id}', [DeviationController::class, 'store_audit_review'])->name('store_audit_review');

/********************************************* Deviation Ends *******************************************/

/********************************************* Deviation Starts *******************************************/

Route::post('failure_investigation_child_1/{id}', [FailureInvestigationController::class, 'failure_investigation_child_1'])->name('failure_investigation_child_1');
Route::post('non_conformances_child_1/{id}', [NonConformaceController::class, 'non_conformances_child_1'])->name('non_conformances_child_1');
Route::post('incident_child_1/{id}', [IncidentController::class, 'incident_child_1'])->name('incident_child_1');

/********************************************* Deviation Ends *******************************************/

// ==============================end ==============================
//! ============================================
//!                    Risk Management
//! ============================================
Route::get('risk-management', [RiskManagementController::class, 'risk']);
Route::get('RiskManagement/{id}', [RiskManagementController::class, 'show'])->name('showRiskManagement');
Route::post('risk_store', [RiskManagementController::class, 'store'])->name('risk_store');
Route::post('riskAssesmentUpdate/{id}', [RiskManagementController::class, 'riskUpdate'])->name('riskUpdate');
Route::post('riskAssesmentStateChange{id}', [RiskManagementController::class, 'riskAssesmentStateChange'])->name('riskAssesmentStateUpdate');
Route::post('reject_Risk/{id}', [RiskManagementController::class, 'RejectStateChange'])->name('reject_Risk');

Route::get('riskAuditTrial/{id}', [RiskManagementController::class, 'riskAuditTrial']);
Route::get('auditDetailsrisk/{id}', [RiskManagementController::class, 'auditDetailsrisk'])->name('showriskAuditDetails');
Route::post('child/{id}', [RiskManagementController::class, 'child'])->name('riskAssesmentChild');



// ======================================================


// ====================================root cause analysis=======================
Route::get('root-cause-analysis', [RootCauseController::class, 'rootcause']);
Route::post('rootstore', [RootCauseController::class, 'root_store'])->name('root_store');
Route::post('rootUpdate/{id}', [RootCauseController::class, 'root_update'])->name('root_update');
Route::get('rootshow/{id}', [RootCauseController::class, 'root_show'])->name('root_show');
Route::post('root/stage/{id}', [RootCauseController::class, 'root_send_stage'])->name('root_send_stage');
Route::post('root/cancel/{id}', [RootCauseController::class, 'root_Cancel'])->name('root_Cancel');
Route::post('root/reject/{id}', [RootCauseController::class, 'root_reject'])->name('root_reject');
Route::get('rootAuditTrial/{id}', [RootCauseController::class, 'rootAuditTrial']);
Route::get('auditDetailsRoot/{id}', [RootCauseController::class, 'auditDetailsroot'])->name('showrootAuditDetails');



// ====================================InternalauditController=======================
Route::post('internalauditreject/{id}', [InternalauditController::class, 'RejectStateChange']);
Route::post('InternalAuditCancel/{id}', [InternalauditController::class, 'InternalAuditCancel']);
Route::post('InternalAuditChild/{id}', [InternalauditController::class, 'internal_audit_child'])->name('internal_audit_child');

// external audit----------------------------

Route::get('show/{id}', [AuditeeController::class, 'show'])->name('showExternalAudit');
Route::post('auditee_store', [AuditeeController::class, 'store'])->name('auditee_store');
Route::post('update/{id}', [AuditeeController::class, 'update'])->name('updateExternalAudit');
Route::post('ExternalAuditStateChange/{id}', [AuditeeController::class, 'ExternalAuditStateChange'])->name('externalAuditStateChange');
Route::post('RejectStateAuditee/{id}', [AuditeeController::class, 'RejectStateChange'])->name('RejectStateAuditee');
Route::post('CancelStateExternalAudit/{id}', [AuditeeController::class, 'externalAuditCancel'])->name('CancelStateExternalAudit');
Route::get('ExternalAuditTrialShow/{id}', [AuditeeController::class, 'AuditTrialExternalShow'])->name('ShowexternalAuditTrial');
Route::get('ExternalAuditTrialDetails/{id}', [AuditeeController::class, 'AuditTrialExternalDetails'])->name('ExternalAuditTrialDetailsShow');
Route::post('child_external/{id}', [AuditeeController::class, 'child_external'])->name('childexternalaudit');

//----------------------Lab Incident view-----------------
Route::get('lab-incident', [LabIncidentController::class, 'labincident']);
//Route::post('RejectStateChange/{id}', [RootCauseController::class, 'RejectStateChange'])->name('RejectStateChange');
// Route::post('RejectStateChange/{id}', [LabIncidentController::class, 'RejectStateChange']);
// Route::post('LabIncidentStateChange/{id}', [LabIncidentController::class, 'LabIncidentStateChange'])->name('StageChangeLabIncident');
Route::post('RejectStateChange/{id}', [LabIncidentController::class, 'RejectStateChange']);
Route::post('StageChangeLabIncident/{id}', [LabIncidentController::class, 'LabIncidentStateChange']);
Route::post('LabIncidentCancel/{id}', [LabIncidentController::class, 'LabIncidentCancelStage']);

Route::get('audit-program', [AuditProgramController::class, 'auditprogram']);






Route::get('data-fields', function () {
    return view('frontend.data-fields');
});
Route::view('emp', 'emp');

Route::view('tasks', 'frontend.tasks');
Route::view('tasks', 'frontend.T');

Route::view('review-details', 'frontend.documents.review-details');
Route::view('audit-trial-inner', 'frontend.documents.audit-trial-inner');
Route::view('new-pdf', 'frontend.documents.new-pdf');
Route::view('new-login', 'frontend.new-login');

// ============================================
//                    TMS
// ============================================
Route::view('activity_log', 'frontend.TMS.activity_log');

Route::view('helpdesk-personnel', 'frontend.helpdesk-personnel');

// Route::view('send-notification', 'frontend.send-notification');

Route::view('designate-proxy', 'frontend.designate-proxy');

Route::view('person-details', 'frontend.person-details');

Route::view('basic-search', 'frontend.basic-search');

//! ============================================ //
//!                    TMS
//! ============================================ //

Route::view('create-training', 'frontend.TMS.create-training');

Route::view('example', 'frontend.TMS.example');

Route::view('create-quiz', 'frontend.TMS.create-quiz');

Route::view('document-view', 'frontend.TMS.document-view');

Route::view('training-page', 'frontend.TMS.training-page');

Route::view('question-training', 'frontend.TMS.question-training');

Route::view('edit-question', 'frontend.TMS.edit-question');

Route::view('change-control-list', 'frontend.change-control.change-control-list');

Route::view('change-control-list-print', 'frontend.change-control.change-control-list-print');

Route::view('change-control-view', 'frontend.change-control.change-control-view');

Route::view('reviewer-panel', 'frontend.change-control.reviewer-panel');

Route::view('change-control-form', 'frontend.change-control.data-fields');

//Route::view('new-change-control', 'frontend.change-control.new-change-control');
Route::get("new-change-control", [CCController::class, "changecontrol"]);

Route::view('audit-pdf', 'frontend.documents.audit-pdf');

Route::view('employee_new', 'frontend.TMS.Employee.employee_new')->name('employee_new');
Route::view('trainer_qualification', 'frontend.TMS.Trainer_qualification.trainer_qualification')->name('trainer_qualification');

// ====================induction training =================

// // Route::view('induction_training', 'frontend.TMS.Induction_training.induction_training')->name('induction_training');
// Route::view('job_training', 'frontend.TMS.Job_Training.job_training')->name('job_training');
Route::get('job_training', [JobTrainingController::class, 'index'])->name('job_training');
Route::get('job_training/show/{id}', [JobTrainingController::class, 'edit'])->name('job_training_view');

Route::post('job_trainingcreate', [JobTrainingController::class, 'store'])->name('job_trainingcreate');
Route::put('job_trainingupdate/{id}', [JobTrainingController::class, 'update'])->name('job_trainingupdate');





Route::get('induction_training', [InductionTrainingcontroller::class, 'index'])->name('induction_training.index');
Route::get('induction_training/show/{id}', [InductionTrainingcontroller::class, 'edit'])->name('induction_training_view');
Route::post('induction_training', [InductionTrainingcontroller::class, 'store'])->name('induction_training.store');
Route::put('induction_training/{id}', [InductionTrainingcontroller::class, 'update'])->name('induction_training.update');


//! ============================================
//!                    RCMS
//! ============================================
Route::get('chart-data', [DesktopController::class, 'fetchChartData']);

Route::view('rcms_login', 'frontend.rcms.login');

Route::view('rcms_dashboard', 'frontend.rcms.dashboard');
Route::get('rcms_desktop', [DesktopController::class, 'rcms_desktop']);
Route::post('dashboard_search', [DesktopController::class, 'dashboard_search'])->name('dashboard_search');
Route::post('dashboard_search', [DesktopController::class, 'main_dashboard_search'])->name('main_dashboard_search');
// Route::view('rcms_desktop', 'frontend.rcms.desktop');

Route::view('rcms_reports', 'frontend.rcms.reports');

Route::view('Quality-Dashboard-Report', 'frontend.rcms.Quality-Dashboard');

Route::view('Supplier-Dashboard-Report', 'frontend.rcms.Supplier-Dashboard');

Route::view('QMSDashboardFormat', 'frontend.rcms.QMSDashboardFormat');



//! ============================================
//!                    FORMS
//! ============================================


Route::view('deviation', 'frontend.forms.deviation');

Route::view('extension_form', 'frontend.forms.extension');

Route::view('cc-form', 'frontend.forms.change-control');

Route::view('audit-management', 'frontend.forms.audit-management');

Route::view('out-of-specification', 'frontend.forms.out-of-specification');

// Route::view('risk-management', 'frontend.forms.risk-management');


Route::view('action-item', 'frontend.forms.action-item');

// Route::view('effectiveness-check', 'frontend.forms.effectiveness-check');
Route::get('effectiveness-check', [EffectivenessCheckController::class, 'effectiveness_check']);

Route::view('quality-event', 'frontend.forms.quality-event');

Route::view('vendor-entity', 'frontend.forms.vendor-entity');

// Route::view('auditee', 'frontend.forms.auditee');
Route::get('auditee', [AuditeeController::class, 'external_audit']);


Route::get('meeting', [ManagementReviewController::class, 'meeting']);

// Route::view('market-complaint', 'frontend.forms.market-complaint');

//Route::view('lab-incident', 'frontend.forms.lab-incident');

Route::view('classroom-training', 'frontend.forms.classroom-training');

Route::view('employee', 'frontend.forms.employee');

Route::view('requirement-template', 'frontend.forms.requirement-template');

Route::view('scar', 'frontend.forms.scar');

Route::view('external-audit', 'frontend.forms.external-audit');

Route::view('contract', 'frontend.forms.contract');

Route::view('supplier', 'frontend.forms.supplier');

Route::view('supplier-initiated-change', 'frontend.forms.supplier-initiated-change');

Route::view('supplier-investigation', 'frontend.forms.supplier-investigation');

Route::view('supplier-issue-notification', 'frontend.forms.supplier-issue-notification');

Route::view('supplier-audit', 'frontend.forms.supplier-audit');

// Route::view('audit', 'frontend.forms.audit');
Route::get('audit', [InternalauditController::class, 'internal_audit']);

Route::view('supplier-questionnaire', 'frontend.forms.supplier-questionnaire');

Route::view('substance', 'frontend.forms.substance');

Route::view('supplier-action-item', 'frontend.forms.supplier-action-item');

Route::view('registration-template', 'frontend.forms.registration-template');

Route::view('project', 'frontend.forms.project');

Route::get('extension', [ExtensionController::class, 'extension_child']);

//Route::view('observation', 'frontend.forms.observation');
Route::get('observation', [ObservationController::class, 'observation']);

Route::view('new-root-cause-analysis', 'frontend.forms.new-root-cause-analysis');

Route::view('help-desk-incident', 'frontend.forms.help-desk-incident');

Route::view('review-management-report', 'frontend.review-management.review-management-report');



//  ===================== OOS OOT OOC Form Route====================================
Route::view('OOT_form', 'frontend.OOT.OOT_form');
Route::get('out_of_calibration', [OOCController::class, 'index'])->name('ooc.index');
Route::get('OOC/view', [OOCController::class, 'edit'])->name('ooc.edit');
Route::post('ooccreate', [OOCController::class, 'create'])->name('oocCreate');
Route::get('OutofCalibrationShow/{id}', [OOCController::class, 'OutofCalibrationShow'])->name('ShowOutofCalibration');
Route::post('updateOutOfCalibration/{id}', [OOCController::class, 'updateOutOfCalibration'])->name('OutOfCalibrationUpdate');
Route::post('OOCStateChange/{id}', [OOCController::class, 'OOCStateChange'])->name('StageChangeOOC');
Route::post('OOCStateChangetwo/{id}', [OOCController::class, 'OOCStateChangetwo'])->name('StageChangeOOCtwo');
Route::post('OOCStateCancel/{id}', [OOCController::class, 'OOCStateCancel'])->name('OOCCancel');
Route::post('RejectoocStateChange/{id}', [OOCController::class, 'RejectoocStateChange'])->name('RejectStateChangeOOC');
Route::post('OOCChildRoot/{id}', [OOCController::class, 'OOCChildRoot'])->name('o_o_c_root_child');
Route::post('OOCChildCapa/{id}', [OOCController::class, 'oo_c_capa_child'])->name('oo_c_capa_child');
Route::get('OOCAuditTrial/{id}', [OOCController::class, 'OOCAuditTrial'])->name('audittrialooc');
Route::get('auditDetailsooc/{id}', [OOCController::class, 'auditDetailsooc'])->name('OOCauditDetails');
Route::get('/rcms/ooc_Audit_Report/{id}', [OOCController::class, 'auditReportooc'])->name('ooc_Audit_Report');





Route::get('out_of_calibration_ooc', [OOCController::class, 'ooc']);


// Route::get('oos_form', [OOSController::class, 'index'])->name('oos.index');
// Route::get('oos_micro', [OOSMicroController::class, 'index'])->name('oos_micro.index');



//============================================ OOS MICRO ROUTE CLOSE ===================================
// Route::view('market_complaint_new', 'frontend.market_complaint.market_complaint_new')->name('market_complaint_new');


// ====================OOS/OOT======================================
Route::view('oos_oot_form', 'frontend.forms.OOS\OOT.oos_oot');
// ====================OOS/OOT======================================



// =================LOGS=========================================

Route::view('change_control_log', 'frontend.forms.Logs.changeControlLog');
Route::view('market_complaint_log', 'frontend.forms.Logs.Market-Complaint-registerLog');
Route::view('incident_log', 'frontend.forms.Logs.incidentLog');
Route::view('risk_management_log', 'frontend.forms.Logs.riskmanagementLog');
Route::view('errata_log', 'frontend.forms.Logs.errata_log');
Route::view('laboratory_log', 'frontend.forms.Logs.laboratoryIncidentLog');
Route::view('capa_log', 'frontend.forms.Logs.capa_log');
Route::view('non_conformance_log', 'frontend.forms.Logs.non_conformance_log');
Route::view('deviation_log', 'frontend.forms.Logs.deviation_log');
Route::view('OOC_log', 'frontend.forms.Logs.OOC_log');
Route::view('OOS_OOT_log', 'frontend.forms.Logs.OOS_OOT_log');
Route::view('Failure_invst_log', 'frontend.forms.Logs.Failure_investigation_Log');
Route::view('internal_audit_log', 'frontend.forms.Logs.Internal_audit_Log');

// =================LOGS=========================================




// ====================OOS/OOT======================================
Route::view('oos_oot_form', 'frontend.forms.OOS\OOT.oos_oot');
// ====================OOS/OOT======================================



/**
 * AJAX ROUTES
 */

Route::get('/sop/users/{id?}', [AjaxController::class, 'getSopTrainingUsers'])->name('sop_training_users');

// ========================Errata==================================
// Route::view('errata_new', 'frontend.errata.errata_new')->name('errata_new');
Route::view('errata_view', 'frontend.errata.errata_view');

// <<<<<<< HEAD

// ================EMPLOYEE & TRAINER===================

Route::post('/tms/employee', [EmployeeController::class, 'store'])->name('employee.store');
Route::post('/tms/trainer', [TrainerController::class, 'store'])->name('trainer.store');
Route::post('/tms/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
Route::post('/tms/trainer/{id}', [TrainerController::class, 'update'])->name('trainer.update');
Route::get('employee_view/{id}', [EmployeeController::class, 'show']);
Route::get('trainer_qualification_view/{id}', [TrainerController::class, 'show']);
Route::post('/tms/employee/sendstage/{id}', [EmployeeController::class, 'sendStage']);
Route::post('/tms/trainer/sendstage/{id}', [TrainerController::class, 'sendStage']);
Route::post('/tms/trainer/rejectStage/{id}', [TrainerController::class, 'rejectStage']);
// =======
Route::post('errata/create{id}', [ErrataController::class, 'create'])->name('errata.create');
Route::post('errata/store', [ErrataController::class, 'store'])->name('errata.store');
Route::get('errata/show/{id}', [ErrataController::class, 'show'])->name('errata.show');
// Route::get('errata/edit/{id}', [ErrataController::class, 'edit'])->name('errata.edit');
Route::put('errata/update/{id}', [Erratacontroller::class, 'update'])->name('errata.update');
Route::get('errataaudittrail/{id}', [ErrataController::class, 'AuditTrial'])->name('errata.audittrail');
Route::get('errataAuditInner/{id}', [ErrataController::class, 'auditDetailsErrata'])->name('errataauditdetails');
Route::post('/errata/cancel/{id}', [ErrataController::class, 'erratacancelstage'])->name('errata.cancel');
Route::get('errata_new', [ErrataController::class, 'index'])->name('errata_new');

// ----------------------Stages----------------------------------------


// extensionchild========================
// Route::view('extension_new', 'frontend.extension.extension_new');
// Route::view('extension_view', 'frontend.extension.extension_view');
Route::get('extension-new', [ExtensionNewController::class, 'index']);
Route::post('extension_new', [ExtensionNewController::class, 'store'])->name('extension_new.store');
Route::get('extension_newshow/{id}', [ExtensionNewController::class, 'show']);

Route::put('extension_new/{id}', [ExtensionNewController::class, 'update'])->name('extension_new.update');
Route::post('extension_send_stage/{id}', [ExtensionNewController::class, 'sendstage'])->name('extension_send_stage');
Route::post('moreinfoState_extension/{id}', [ExtensionNewController::class, 'moreinfoStateChange'])->name('moreinfoState_extension');
Route::post('RejectState_extension/{id}', [ExtensionNewController::class, 'reject'])->name('RejectState_extension');
// Route::get('RejectState_extension/{id}', [ExtensionNewController::class, 'reject'])->name('RejectState_extension');


Route::get('trainer_qualification', [TrainerController::class, 'index'])->name('trainer_qualification');

//=====================================================================
// >>>>>>> B-backup

//=====================  Print Request ================================================
// Route::resource('print-request', PrintRequestController::class);
Route::get('print-request', [PrintRequestController::class, 'index']);
Route::get('print-request/create', [PrintRequestController::class, 'create'])->name('print_request.create');
Route::post('print-request/store', [PrintRequestController::class, 'store'])->name('print-request.store');
Route::get('print-request/edit/{id}', [PrintRequestController::class, 'show']);
Route::post('print-request/update/{id}', [PrintRequestController::class, 'update'])->name('print-request.update');
Route::post('print-request/stage/{id}', [PrintRequestController::class, 'stageChange'])->name('print-request.stage');
Route::post('print-request/stage-reject/{id}', [PrintRequestController::class, 'stageReject'])->name('print-request.stagereject');
Route::post('print-request/stage-cancel/{id}', [PrintRequestController::class, 'erratacancelstage'])->name('print-request.cancel');

Route::get('print-request/single-report/{id}', [PrintRequestController::class, 'singleReport']);
