<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * QuestionnaireController (Admin): Manajemen kuesioner & CRUD butir soal.
 * Admin/Peneliti dapat menambah, mengedit, dan menghapus soal per kuesioner.
 */
class QuestionnaireController extends BaseController
{
    /**
     * Daftar semua kuesioner beserta jumlah soal & responden.
     */
    public function index(): string
    {
        $questionnaireModel = new \App\Models\QuestionnaireModel();
        $questionnaires = $questionnaireModel->getQuestionnairesWithStats();

        return $this->renderView('admin/questionnaires/index', [
            'title'          => 'Manajemen Kuesioner — SIREMAJA',
            'questionnaires' => $questionnaires,
        ]);
    }

    /**
     * Tampilkan daftar soal milik kuesioner tertentu.
     *
     * @param int $id Questionnaire ID
     */
    public function questions(string $hashId)
    {
        $id = decode_id($hashId);
        if (!$id) return redirect()->to(base_url('admin/questionnaires'))->with('error', 'Invalid URL.');

        $questionnaireModel = new \App\Models\QuestionnaireModel();
        $questionnaire = $questionnaireModel->find($id);
        if (! $questionnaire) {
            return redirect()->to(base_url('admin/questionnaires'))
                ->with('error', 'Kuesioner tidak ditemukan.');
        }

        $questionModel = new \App\Models\QuestionModel();
        $questions = $questionModel->where('questionnaire_id', $id)
            ->orderBy('order_number', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return $this->renderView('admin/questionnaires/questions', [
            'title'         => 'Soal: ' . $questionnaire['title'],
            'questionnaire' => $questionnaire,
            'questions'     => $questions,
        ]);
    }

    // =========================================================
    // CRUD SOAL
    // =========================================================

    /**
     * Form tambah soal baru ke kuesioner.
     *
     * @param int $questionnaireId
     */
    public function createQuestion(string $hashId)
    {
        $questionnaireId = decode_id($hashId);
        if (!$questionnaireId) return redirect()->to(base_url('admin/questionnaires'))->with('error', 'Invalid URL.');

        $questionnaireModel = new \App\Models\QuestionnaireModel();
        $questionnaire = $questionnaireModel->find($questionnaireId);
        if (! $questionnaire) {
            return redirect()->to(base_url('admin/questionnaires'))
                ->with('error', 'Kuesioner tidak ditemukan.');
        }

        return $this->renderView('admin/questionnaires/question_form', [
            'title'         => 'Tambah Soal — ' . $questionnaire['title'],
            'questionnaire' => $questionnaire,
            'question'      => null,
            'action'        => base_url("admin/questionnaires/{$questionnaireId}/questions/store"),
        ]);
    }

    /**
     * Simpan soal baru.
     *
     * @param int $questionnaireId
     */
    public function storeQuestion(string $hashId)
    {
        $questionnaireId = decode_id($hashId);
        if (!$questionnaireId) return redirect()->to(base_url('admin/questionnaires'))->with('error', 'Invalid URL.');

        $questionnaireModel = new \App\Models\QuestionnaireModel();
        $questionnaire = $questionnaireModel->find($questionnaireId);
        if (! $questionnaire) {
            return redirect()->to(base_url('admin/questionnaires'))
                ->with('error', 'Kuesioner tidak ditemukan.');
        }

        $rules = [
            'question_text' => 'required|min_length[5]',
            'question_type' => 'required|in_list[multiple_choice,likert,text]',
            'weight'        => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $type          = $this->request->getPost('question_type');
        $options       = $this->buildOptionsJson($type);
        $correctAnswer = null;

        if ($type === 'multiple_choice') {
            $correctAnswer = $this->request->getPost('correct_answer') ?: null;
        }

        $questionModel = new \App\Models\QuestionModel();
        
        // Auto-urutan: ambil max order_number + 1
        $maxOrderData = $questionModel->selectMax('order_number')
            ->where('questionnaire_id', $questionnaireId)
            ->first();
        $maxOrder = $maxOrderData['order_number'] ?? 0;

        $questionModel->insert([
            'questionnaire_id' => $questionnaireId,
            'question_text'    => $this->request->getPost('question_text'),
            'question_type'    => $type,
            'options'          => $options,
            'weight'           => $this->request->getPost('weight'),
            'correct_answer'   => $correctAnswer,
            'order_number'     => (int) ($this->request->getPost('order_number') ?: ($maxOrder + 1)),
        ]);

        return redirect()->to(base_url("admin/questionnaires/{$questionnaireId}/questions"))
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Form edit soal.
     *
     * @param int $questionnaireId
     * @param int $questionId
     */
    public function editQuestion(string $hashQId, string $hashQuestionId)
    {
        $questionnaireId = decode_id($hashQId);
        $questionId = decode_id($hashQuestionId);
        if (!$questionnaireId || !$questionId) return redirect()->to(base_url('admin/questionnaires'))->with('error', 'Invalid URL.');

        $questionnaireModel = new \App\Models\QuestionnaireModel();
        $questionModel = new \App\Models\QuestionModel();
        
        $questionnaire = $questionnaireModel->find($questionnaireId);
        $question      = $questionModel->where('id', $questionId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();

        if (! $questionnaire || ! $question) {
            return redirect()->to(base_url("admin/questionnaires/{$questionnaireId}/questions"))
                ->with('error', 'Soal tidak ditemukan.');
        }

        // Parse options JSON untuk ditampilkan di form
        $question['options_decoded'] = json_decode($question['options'] ?? '[]', true) ?: [];

        return $this->renderView('admin/questionnaires/question_form', [
            'title'         => 'Edit Soal — ' . $questionnaire['title'],
            'questionnaire' => $questionnaire,
            'question'      => $question,
            'action'        => base_url("admin/questionnaires/{$questionnaireId}/questions/{$questionId}/update"),
        ]);
    }

    /**
     * Update soal.
     *
     * @param int $questionnaireId
     * @param int $questionId
     */
    public function updateQuestion(string $hashQId, string $hashQuestionId)
    {
        $questionnaireId = decode_id($hashQId);
        $questionId = decode_id($hashQuestionId);
        if (!$questionnaireId || !$questionId) return redirect()->to(base_url('admin/questionnaires'))->with('error', 'Invalid URL.');

        $questionModel = new \App\Models\QuestionModel();
        $question = $questionModel->where('id', $questionId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();

        if (! $question) {
            return redirect()->to(base_url("admin/questionnaires/{$hashQId}/questions"))
                ->with('error', 'Soal tidak ditemukan.');
        }

        $rules = [
            'question_text' => 'required|min_length[5]',
            'question_type' => 'required|in_list[multiple_choice,likert,text]',
            'weight'        => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $type          = $this->request->getPost('question_type');
        $options       = $this->buildOptionsJson($type);
        $correctAnswer = null;

        if ($type === 'multiple_choice') {
            $correctAnswer = $this->request->getPost('correct_answer') ?: null;
        }

        $questionModel->update($questionId, [
            'question_text'  => $this->request->getPost('question_text'),
            'question_type'  => $type,
            'options'        => $options,
            'weight'         => $this->request->getPost('weight'),
            'correct_answer' => $correctAnswer,
            'order_number'   => (int) ($this->request->getPost('order_number') ?: $question['order_number']),
        ]);

        return redirect()->to(base_url("admin/questionnaires/{$questionnaireId}/questions"))
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Hapus soal.
     *
     * @param int $questionnaireId
     * @param int $questionId
     */
    public function deleteQuestion(string $hashQId, string $hashQuestionId)
    {
        $questionnaireId = decode_id($hashQId);
        $questionId = decode_id($hashQuestionId);
        if (!$questionnaireId || !$questionId) return redirect()->to(base_url('admin/questionnaires'))->with('error', 'Invalid URL.');

        $questionModel = new \App\Models\QuestionModel();
        $question = $questionModel->where('id', $questionId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();

        if (! $question) {
            return redirect()->to(base_url("admin/questionnaires/{$questionnaireId}/questions"))
                ->with('error', 'Soal tidak ditemukan.');
        }

        // Audit log
        $auditModel = new \App\Models\AuditLogModel();
        $auditModel->insert([
            'user_id'        => session()->get('user_id'),
            'action'         => 'delete_question',
            'table_affected' => 'questions',
            'record_id'      => $questionId,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $questionModel->delete($questionId);

        return redirect()->to(base_url("admin/questionnaires/{$questionnaireId}/questions"))
            ->with('success', 'Soal berhasil dihapus.');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Bangun JSON options berdasarkan tipe soal dari POST data.
     *
     * @param string $type
     */
    private function buildOptionsJson(string $type): ?string
    {
        if ($type === 'multiple_choice') {
            // Ambil opsi dari field options_key[] & options_val[]
            $keys = $this->request->getPost('options_key') ?: [];
            $vals = $this->request->getPost('options_val') ?: [];
            $opts = [];
            foreach ($keys as $i => $k) {
                if (trim($k) !== '' && isset($vals[$i]) && trim($vals[$i]) !== '') {
                    $opts[trim($k)] = trim($vals[$i]);
                }
            }
            return ! empty($opts) ? json_encode($opts, JSON_UNESCAPED_UNICODE) : null;
        }

        if ($type === 'likert') {
            return json_encode([
                'min'       => (int) ($this->request->getPost('likert_min')       ?: 1),
                'max'       => (int) ($this->request->getPost('likert_max')       ?: 5),
                'min_label' => $this->request->getPost('likert_min_label') ?: 'Sangat Tidak Setuju',
                'max_label' => $this->request->getPost('likert_max_label') ?: 'Sangat Setuju',
            ], JSON_UNESCAPED_UNICODE);
        }

        return null; // text type — no options
    }
}
