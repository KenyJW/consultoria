<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\IsoControl;
use App\Models\Question;

final class QuestionController extends Controller
{
    private Question $questions;
    private IsoControl $controls;

    public function __construct()
    {
        $this->questions = new Question();
        $this->controls = new IsoControl();
    }

    public function index(): void
    {
        Middleware::auth();
        $search = trim((string) ($_GET['q'] ?? ''));
        $controlId = (int) ($_GET['control_id'] ?? 0);
        $sort = (string) ($_GET['sort'] ?? 'created_at');
        $direction = (string) ($_GET['direction'] ?? 'desc');
        $page = (int) ($_GET['page'] ?? 1);

        $this->view('questions/index', [
            'title' => 'Preguntas del instrumento',
            'pagination' => $this->questions->paginateList($search, $controlId, $sort, $direction, $page),
            'controls' => $this->controls->activeOptions(),
            'search' => $search,
            'controlId' => $controlId,
            'sort' => $sort,
            'direction' => $direction,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin']);
        $this->view('questions/form', [
            'title' => 'Nueva pregunta',
            'question' => null,
            'controls' => $this->controls->activeOptions(),
            'action' => '/questions/store',
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin']);
        $data = $this->validatedData('/questions/create');
        $this->questions->create($data);
        Flash::success('Pregunta creada correctamente.');
        $this->redirect('/questions');
    }

    public function show(): void
    {
        Middleware::auth();
        $question = $this->questions->find((int) ($_GET['id'] ?? 0));
        if (! $question) {
            Flash::error('Pregunta no encontrada.');
            $this->redirect('/questions');
        }
        $this->view('questions/show', ['title' => 'Detalle de la pregunta', 'question' => $question]);
    }

    public function edit(): void
    {
        Middleware::roles(['admin']);
        $question = $this->questions->find((int) ($_GET['id'] ?? 0));
        if (! $question) {
            Flash::error('Pregunta no encontrada.');
            $this->redirect('/questions');
        }
        $this->view('questions/form', [
            'title' => 'Editar pregunta',
            'question' => $question,
            'controls' => $this->controls->activeOptions(),
            'action' => '/questions/update',
        ]);
    }

    public function update(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData('/questions/edit?id=' . $id);
        $this->questions->update($id, $data);
        Flash::success('Pregunta actualizada correctamente.');
        $this->redirect('/questions');
    }

    public function toggle(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/questions');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';
        $this->questions->setStatus($id, $status);
        Flash::success('Estado de la pregunta actualizado.');
        $this->redirect('/questions');
    }

    private function validatedData(string $failureRedirect): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/questions');
        }

        $weight = (float) ($_POST['weight'] ?? 1);
        if ($weight < 0.1) {
            $weight = 1.0;
        }

        $data = [
            'control_id' => (int) ($_POST['control_id'] ?? 0),
            'question' => trim((string) ($_POST['question'] ?? '')),
            'weight' => $weight,
            'status' => (string) ($_POST['status'] ?? 'active'),
        ];

        $validator = (new Validator())
            ->required('question', $data['question'], 'Pregunta')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($data['control_id'] <= 0) {
            $_SESSION['_old'] = $data;
            Flash::error('Seleccione un control valido.');
            $this->redirect($failureRedirect);
        }

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($failureRedirect);
        }

        return $data;
    }
}
