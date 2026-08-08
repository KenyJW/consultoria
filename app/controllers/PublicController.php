<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

/** Pagina publica de inicio (landing), mostrada solo a visitantes sin sesion. */
final class PublicController extends Controller
{
    public function landing(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('public/landing', ['title' => 'DataSolutions CR — Consultoría en Administración de Bases de Datos'], 'none');
    }
}
