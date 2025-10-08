<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::resource('introducers', \App\Http\Controllers\IntroducerController::class);
    
    Route::post('country-list', [\App\Helpers\Helper::class, 'getCountries'])->name('country-list');
    Route::post('state-list', [\App\Helpers\Helper::class, 'getStatesByCountry'])->name('state-list');
    Route::post('city-list', [\App\Helpers\Helper::class, 'getCitiesByState'])->name('city-list');
    Route::post('user-list', [\App\Helpers\Helper::class, 'getUsers'])->name('user-list');
    Route::post('holder-list', [\App\Helpers\Helper::class, 'getHolders'])->name('holder-list');
    Route::post('insured-list', [\App\Helpers\Helper::class, 'getInsureds'])->name('insured-list');
    Route::post('document-list', [\App\Helpers\Helper::class, 'getDocuments'])->name('document-list');
    Route::post('introducer-list', [\App\Helpers\Helper::class, 'getIntroducers'])->name('introducer-list');
    Route::post('introducer-details', [\App\Helpers\Helper::class, 'getIntroducerDetails'])->name('introducer-details');

    Route::get('get-docs', [\App\Http\Controllers\CaseController::class, 'getDocs'])->name('get-docs');
    Route::get('case-status-change', [\App\Http\Controllers\CaseController::class, 'caseStatusChange'])->name('case-status-change');
    Route::get('case-liklihood-change', [\App\Http\Controllers\CaseController::class, 'caseLiklihoodChange'])->name('case-liklihood-change');

    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    Route::get('cases/create/{id?}', [\App\Http\Controllers\CaseController::class, 'create'])->name('cases.create');
    Route::get('cases/edit/{id?}', [\App\Http\Controllers\CaseController::class, 'edit'])->name('cases.edit');
    Route::get('cases/view/{id?}', [\App\Http\Controllers\CaseController::class, 'show'])->name('cases.view');
    Route::get('cases', [\App\Http\Controllers\CaseController::class, 'index'])->name('cases.index');
    Route::match(['GET', 'POST'], 'cases/manage-manager/{id?}', [\App\Http\Controllers\CaseController::class, 'caseManagement'])->name('cases.case-manager');

    Route::post('cases/submission', [\App\Http\Controllers\CaseController::class, 'submission'])->name('case.submission');
    Route::post('cases/auto-save', [\App\Http\Controllers\CaseController::class, 'autoSave'])->name('case.auto-save');
    Route::get('cases/get-communications', [\App\Http\Controllers\CaseController::class, 'getCommunications'])->name('case.get-communications');
    Route::get('cases/get-case-file-notes', [\App\Http\Controllers\CaseController::class, 'getCaseFileNotes'])->name('case.get-case-file-notes');
    Route::post('cases/get-insured-lives', [\App\Http\Controllers\CaseController::class, 'getInsuredLives'])->name('case.getInsuredLives');
    Route::post('cases/get-insured-lives-sidebar', [\App\Http\Controllers\CaseController::class, 'getInsuredLivesSidebar'])->name('case.getInsuredLivesSidebar');
    Route::post('cases/get-insured-life', [\App\Http\Controllers\CaseController::class, 'getInsuredLife'])->name('case.getInsuredLife');
    Route::post('cases/delete-insured-life', [\App\Http\Controllers\CaseController::class, 'deleteInsuredLife'])->name('case.deleteInsuredLife');
    Route::post('cases/get-policyholders-sidebar', [\App\Http\Controllers\CaseController::class, 'getPolicyHoldersSidebar'])->name('case.getPolicyHoldersSidebar');
    Route::post('cases/get-policyholder', [\App\Http\Controllers\CaseController::class, 'getPolicyHolder'])->name('case.getPolicyHolder');
    Route::post('cases/delete-policyholder', [\App\Http\Controllers\CaseController::class, 'deletePolicyHolder'])->name('case.deletePolicyHolder');
    Route::post('cases/get-policycontrollers-sidebar', [\App\Http\Controllers\CaseController::class, 'getPolicyControllersSidebar'])->name('case.getPolicyControllersSidebar');
    Route::post('cases/get-policycontroller', [\App\Http\Controllers\CaseController::class, 'getPolicyController'])->name('case.getPolicyController');
    Route::post('cases/delete-policycontroller', [\App\Http\Controllers\CaseController::class, 'deletePolicyController'])->name('case.deletePolicyController');
    Route::post('cases/get-beneficiaries', [\App\Http\Controllers\CaseController::class, 'getBeneficiaries'])->name('case.getBeneficiaries');
    Route::post('cases/get-beneficiary', [\App\Http\Controllers\CaseController::class, 'getBeneficiary'])->name('case.getBeneficiary');
    Route::post('cases/delete-beneficiary', [\App\Http\Controllers\CaseController::class, 'deleteBeneficiary'])->name('case.deleteBeneficiary');
    Route::post('cases/get-beneficiaries-sidebar', [\App\Http\Controllers\CaseController::class, 'getBeneficiariesSidebar'])->name('case.getBeneficiariesSidebar');
    Route::post('cases/get-introducers-sidebar', [\App\Http\Controllers\CaseController::class, 'getIntroducersSidebar'])->name('case.getIntroducersSidebar');
    Route::post('cases/get-introducer', [\App\Http\Controllers\CaseController::class, 'getIntroducer'])->name('case.getIntroducer');
    Route::post('cases/delete-introducer', [\App\Http\Controllers\CaseController::class, 'deleteIntroducer'])->name('case.deleteIntroducer');

    Route::post('upload-document', [\App\Http\Controllers\CaseController::class, 'uploadDoc'])->name('upload-document');
    Route::post('cases/delete-communication', [\App\Http\Controllers\CaseController::class, 'deleteCommunication'])->name('case.delete-communication');
    Route::post('cases/delete-note', [\App\Http\Controllers\CaseController::class, 'deleteNote'])->name('case.delete-note');
    Route::get('remove-upload-form-doc/{id}', [\App\Http\Controllers\CaseController::class, 'deleteDocument'])->name('remove-upload-form-doc');
    Route::post('downloadable-documents', [\App\Http\Controllers\CaseController::class, 'storeDownloadableDocument'])->name('downloadable-documents.store');
    Route::post('downloadable-documents/update-ordering', [\App\Http\Controllers\CaseController::class, 'updateDownloadableDocumentsOrdering'])->name('downloadable-documents.update-ordering');
    Route::post('downloadable-documents/remove-file', [\App\Http\Controllers\CaseController::class, 'removeDownloadableDocumentFile'])->name('downloadable-documents.remove-file');

    Route::post('tooltips/update', [\App\Http\Controllers\CaseController::class, 'tooltipUpdate'])->name('tooltips.update');
    Route::get('tooltips/{element}', [\App\Http\Controllers\CaseController::class, 'tooltipShow'])->name('tooltips.show');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('policies-by-status', [\App\Http\Controllers\ReportsController::class, 'policiesByStatus'])->name('policies-by-status');
        Route::get('policies-by-status/export', [\App\Http\Controllers\ReportsController::class, 'exportPoliciesByStatus'])->name('policies-by-status.export');
        Route::get('new-policies', [\App\Http\Controllers\ReportsController::class, 'newPolicies'])->name('new-policies');
        Route::get('new-policies/export', [\App\Http\Controllers\ReportsController::class, 'exportNewPolicies'])->name('new-policies.export');
        Route::get('top-introducers', [\App\Http\Controllers\ReportsController::class, 'topIntroducers'])->name('top-introducers');
        Route::get('status-report', [\App\Http\Controllers\ReportsController::class, 'statusReport'])->name('status-report');
        Route::get('status-report/export', [\App\Http\Controllers\ReportsController::class, 'exportStatusReport'])->name('status-report.export');
        Route::get('missing-docs-report/export', [\App\Http\Controllers\ReportsController::class, 'exportMissingDocsReport'])->name('missing-docs.export');
        Route::get('missing-expired-documents', [\App\Http\Controllers\ReportsController::class, 'missingExpiredDocuments'])->name('missing-expired-documents');
    });

    Route::prefix('mailbox')->name('mailbox.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MailboxController::class, 'index'])->name('index');
        Route::get('/unread-count', [\App\Http\Controllers\MailboxController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/{id}/mark-read', [\App\Http\Controllers\MailboxController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{id}/mark-unread', [\App\Http\Controllers\MailboxController::class, 'markAsUnread'])->name('mark-unread');
        Route::delete('/{id}/delete', [\App\Http\Controllers\MailboxController::class, 'delete'])->name('delete');
        Route::get('/{id}', [\App\Http\Controllers\MailboxController::class, 'show'])->name('show');
    });
});