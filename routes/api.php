<?php

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\FileController;
use \App\Http\Controllers\BookingController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JoinGroupController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LogController;

Route::middleware('aspect')->group(function () {
    Route::post('register', [UserController::class, 'Register']);
    Route::post('login', [UserController::class, 'Login']);
});

Route::get('download/{id}',[FileController::class,'download']);
Route::get('downloadAsBytes/{id}',[FileController::class,'downloadAsBytes']);
Route::get('downloadBackup/{file_name}',[BookingController::class,'downloadBackup']);
Route::middleware(['auth:api', 'aspect'])->group(function () {


    Route::get('ShowAllFiles/{user_group_id}',[FileController::class,'ShowAllFiles']);
    Route::post('checkin',[BookingController::class,'check_in']);
    Route::post('checkout',[BookingController::class,'check_out']);
    Route::get('ShowBookedFiles',[FileController::class,'ShowBookedFiles']);
    Route::post('upload',[FileController::class,'upload']);
    Route::post('updateFile',[FileController::class,'updateFile']);
    Route::get('showGroupsUserNotTheAdminIn',[GroupController::class,'showGroupsUserNotTheAdminIn']);
    Route::get('ShowAllGroupsNotIn',[GroupController::class,'ShowAllGroupsNotIn']);
    Route::post('JoinRequest/{group_id}',[JoinGroupController::class,'joinRequest']);
    Route::post('logout',[UserController::class,'Logout']);

    //------------------------------------------notification
    Route::get('notifications', [NotificationsController::class, 'getNotifications']);
    Route::post('notifications/{id}/mark-as-read', [NotificationsController::class, 'markAsRead']);

    Route::post('create-new-group', [GroupController::class, 'createGroup']);
    Route::post('assignNewUsersToGroup', [GroupController::class, 'assignNewUsersToGroup']);
    Route::post('isGroupAdmin', [GroupController::class, 'isGroupAdmin']);
    Route::post('/groups/eligible-users', [GroupController::class, 'getEligibleUsers']);
    Route::post('/groups/search-user', [GroupController::class, 'searchUserForGroup']);
    Route::get('/invitations/sent', [GroupController::class, 'getSentInvitations']);
    Route::post('/invitations/{id}/reject', [GroupController::class, 'rejectInvitation']);
    Route::post('/invitations/{id}/accept', [GroupController::class, 'acceptInvitation']);
    Route::get('/invitations/received', [GroupController::class, 'getReceivedInvitations']);
    Route::get('seeFileReport/{file_id}',[ReportController::class,'SeeFileReport']);
    Route::get('Backups/{file_id}',[BookingController::class,'showBackups']);
    Route::get('/groups/{group_id}/users', [GroupController::class, 'getGroupUser']);


    Route::post('AcceptJoin',[JoinGroupController::class,'AcceptJoin']);
    Route::post('RejectJoin',[JoinGroupController::class,'RejectJoin']);
    Route::get('getAllRequest/{group_id}',[JoinGroupController::class,'getAllRequests']);
    Route::get('acceptFile',[FileController::class,'acceptFile']);
    Route::delete('rejectFile',[FileController::class,'rejectFile']);
    Route::delete('deleteFile',[FileController::class,'rejectFile']);
    Route::get('ShowDownloadedFiles/{group_id}',[FileController::class,'ShowDownloadedFiles']);
    Route::post('/invitations/send-invitation', [GroupController::class, 'sendInvitation']);
    Route::get('/groups/get-users-not-group-admin', [GroupController::class, 'getUsersNotGroupAdmin']);
    Route::get('getAllGroupsAsAdmin', [GroupController::class, 'getAllGroupsAsAdmin']);
    Route::get('seeUserReport/{group_id}',[ReportController::class,'SeeReport']);

    Route::get('exportAllFilesLogs/{format}', [ReportController::class, 'exportAllFilesLogs']);
    Route::get('exportAllUsersLogs/{format}', [ReportController::class, 'exportAllUsersLogs']);
    Route::get('exportAllGroupsLogs/{format}', [ReportController::class, 'exportAllGroupsLogs']);
    Route::get('exportFileLog/{format}', [ReportController::class, 'exportFileLog']);
    Route::get('exportUserLog/{format}', [ReportController::class, 'exportUserLog']);
    Route::get('exportGroupLog/{format}', [ReportController::class, 'exportGroupLog']);
    Route::get('exportUserFileLog/{format}', [ReportController::class, 'exportUserFileLog']);
    Route::get('exportFileUpdatesLogs/{format}', [ReportController::class, 'exportFileUpdatesLogs']);
    Route::get('exportUserUpdatesLogs/{format}', [ReportController::class, 'exportUserUpdatesLogs']);
    Route::get('exportAllLogs/{format}', [ReportController::class, 'exportAllLogs']);
    Route::get('exportFailedLogs/{format}', [ReportController::class, 'exportFailedLogs']);
    Route::get('exportSuccessfulLogs/{format}', [ReportController::class, 'getSuccessfulLogs']);


    Route::prefix('admin')->middleware(['authorize:super_admin'])->group(function () {

        Route::get('/files', [AdminController::class, 'getAllFiles']);
        Route::get('/groups', [AdminController::class, 'getAllGroups']);
        Route::get('/users', [AdminController::class, 'getAllUsers']);

        Route::get('/files/logs', [AdminController::class, 'getAllFilesLogs']);
        Route::get('/users/logs', [AdminController::class, 'getAllUsersLogs']);
        Route::get('/groups/logs', [AdminController::class, 'getAllGroupsLogs']);
        Route::post('/file/logs', [AdminController::class, 'getFileLog']);
        Route::post('/user/logs', [AdminController::class, 'getUserLog']);
        Route::post('/group/logs', [AdminController::class, 'getGroupLog']);
        Route::post('/users/files/logs', [AdminController::class, 'getUserFileLog']);
        Route::post('/file/updates/logs', [AdminController::class, 'getFileUpdatesLogs']);
        Route::post('/users/updates/logs', [AdminController::class, 'getUserUpdatesLogs']);
        Route::get('/logs', [AdminController::class, 'getAllLogs']);
        Route::get('/logs/failed' , [AdminController::class, 'getFailedLogs']);
        Route::get('/logs/Successful' , [AdminController::class, 'getSuccessfulLogs']);
        //testing

    });

 
    Route::middleware([
        'authorize:group_admin'])->group(function () {
        Route::post('/users/logs', [LogController::class, 'getUserLogs']);
        Route::post('test', [GroupController::class, 'test']);
    });


    Route::middleware([
        'authorize:group_member',
        'aspect'])->group(function () {
        Route::post('/files/logs', [LogController::class, 'getFileLogs']);
    });

    Route::post('/logs/getLogsForFile/{file_id}', [LogController::class, 'getLogsForFile']);
    Route::post('/logs', [LogController::class, 'readLog']);
    Route::post('/logs/getLogsForFile/{file_id}', [LogController::class, 'getLogsForFile']);
    Route::post('/logs/getLogsForFile/{file_id}', [LogController::class, 'getLogsForFile']);
    Route::post('/compare-files', [FileController::class, 'compareFiles']);
});
