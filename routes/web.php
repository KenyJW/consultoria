<?php
declare(strict_types=1);

use App\Controllers\AreaController;
use App\Controllers\AuditController;
use App\Controllers\ComparisonController;
use App\Controllers\DashboardController;
use App\Controllers\IsoControlController;
use App\Controllers\IsoDomainController;
use App\Controllers\LoginController;
use App\Controllers\OrganizationController;
use App\Controllers\QuestionController;
use App\Controllers\RecommendationController;
use App\Controllers\UserController;

$router->get('/', [LoginController::class, 'show']);
$router->get('/login', [LoginController::class, 'show']);
$router->post('/login', [LoginController::class, 'authenticate']);
$router->post('/logout', [LoginController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users/store', [UserController::class, 'store']);
$router->get('/users/edit', [UserController::class, 'edit']);
$router->post('/users/update', [UserController::class, 'update']);
$router->post('/users/delete', [UserController::class, 'destroy']);

$router->get('/organizations', [OrganizationController::class, 'index']);
$router->get('/organizations/create', [OrganizationController::class, 'create']);
$router->post('/organizations/store', [OrganizationController::class, 'store']);
$router->get('/organizations/show', [OrganizationController::class, 'show']);
$router->get('/organizations/edit', [OrganizationController::class, 'edit']);
$router->post('/organizations/update', [OrganizationController::class, 'update']);
$router->post('/organizations/toggle', [OrganizationController::class, 'toggle']);

$router->get('/areas', [AreaController::class, 'index']);
$router->get('/areas/create', [AreaController::class, 'create']);
$router->post('/areas/store', [AreaController::class, 'store']);
$router->get('/areas/show', [AreaController::class, 'show']);
$router->get('/areas/edit', [AreaController::class, 'edit']);
$router->post('/areas/update', [AreaController::class, 'update']);
$router->post('/areas/toggle', [AreaController::class, 'toggle']);

$router->get('/domains', [IsoDomainController::class, 'index']);
$router->get('/domains/create', [IsoDomainController::class, 'create']);
$router->post('/domains/store', [IsoDomainController::class, 'store']);
$router->get('/domains/show', [IsoDomainController::class, 'show']);
$router->get('/domains/edit', [IsoDomainController::class, 'edit']);
$router->post('/domains/update', [IsoDomainController::class, 'update']);
$router->post('/domains/toggle', [IsoDomainController::class, 'toggle']);

$router->get('/controls', [IsoControlController::class, 'index']);
$router->get('/controls/create', [IsoControlController::class, 'create']);
$router->post('/controls/store', [IsoControlController::class, 'store']);
$router->get('/controls/show', [IsoControlController::class, 'show']);
$router->get('/controls/edit', [IsoControlController::class, 'edit']);
$router->post('/controls/update', [IsoControlController::class, 'update']);
$router->post('/controls/toggle', [IsoControlController::class, 'toggle']);

$router->get('/questions', [QuestionController::class, 'index']);
$router->get('/questions/create', [QuestionController::class, 'create']);
$router->post('/questions/store', [QuestionController::class, 'store']);
$router->get('/questions/show', [QuestionController::class, 'show']);
$router->get('/questions/edit', [QuestionController::class, 'edit']);
$router->post('/questions/update', [QuestionController::class, 'update']);
$router->post('/questions/toggle', [QuestionController::class, 'toggle']);

$router->get('/audits', [AuditController::class, 'index']);
$router->get('/audits/create', [AuditController::class, 'create']);
$router->post('/audits/store', [AuditController::class, 'store']);
$router->get('/audits/show', [AuditController::class, 'show']);
$router->get('/audits/edit', [AuditController::class, 'edit']);
$router->post('/audits/update', [AuditController::class, 'update']);
$router->get('/audits/areas-json', [AuditController::class, 'areasJson']);
$router->get('/audits/run', [AuditController::class, 'run']);
$router->post('/audits/save', [AuditController::class, 'save']);
$router->post('/audits/reopen', [AuditController::class, 'reopen']);
$router->post('/audits/upload-evidence', [AuditController::class, 'uploadEvidence']);
$router->post('/audits/delete-evidence', [AuditController::class, 'deleteEvidence']);
$router->get('/audits/report', [AuditController::class, 'report']);

$router->get('/recommendations', [RecommendationController::class, 'index']);
$router->get('/recommendations/audit', [RecommendationController::class, 'forAudit']);
$router->post('/recommendations/store', [RecommendationController::class, 'store']);
$router->post('/recommendations/update', [RecommendationController::class, 'update']);
$router->post('/recommendations/delete', [RecommendationController::class, 'delete']);

$router->get('/comparison', [ComparisonController::class, 'show']);
