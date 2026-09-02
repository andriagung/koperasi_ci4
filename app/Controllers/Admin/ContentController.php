<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\ContentModel;

/**
 * ContentController (Admin): CRUD Materi Edukasi.
 */
class ContentController extends BaseController
{
    protected AuditLogModel $auditModel;
    protected ContentModel $contentModel;

    public function __construct()
    {
        $this->auditModel   = new AuditLogModel();
        $this->contentModel = new ContentModel();
    }

    public function index(): string
    {
        return $this->renderView('admin/contents/index', [
            'title'    => 'Materi Edukasi — SIREMAJA',
            // Contents passed via AJAX SSP
        ]);
    }

    /** DataTables SSP for Contents */
    public function ajaxList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Direct access not allowed');
        }

        $postData = $this->request->getPost();
        
        $result = $this->contentModel->getDatatablesAdmin($postData);

        $data = [];
        $no = $postData['start'] ?? 0;

        foreach ($result['data'] as $content) {
            $no++;
            $row = [];
            
            // 0: Order
            $row[] = '<div class="text-center fw-bold text-white">' . $content['order_number'] . '</div>';
            
            // 1: Title & Category
            $badges = [
                'hiv_dasar' => ['#38BDF8', 'rgba(14,165,233,0.15)', 'HIV Dasar'],
                'penularan_pencegahan' => ['#F43F5E', 'rgba(244,63,94,0.15)', 'Pencegahan'],
                'perilaku_berisiko' => ['#F59E0B', 'rgba(245,158,11,0.15)', 'Risiko'],
                'umum' => ['#A78BFA', 'rgba(139,92,246,0.15)', 'Umum'],
            ];
            $cat = $content['category'];
            $c = $badges[$cat] ?? ['#94A3B8', 'rgba(148,163,184,0.15)', 'Lainnya'];
            $badgeHtml = '<span class="badge ms-2" style="font-size:0.65rem;background:' . $c[1] . ';color:' . $c[0] . '">' . $c[2] . '</span>';
            $row[] = '<div class="fw-semibold text-white">' . esc($content['title']) . $badgeHtml . '</div>';
            
            // 2: Author
            $row[] = '<div style="font-size:0.85rem">' . esc($content['author_name'] ?? 'Admin') . '</div>';
            
            // 3: Status
            $st = $content['status'];
            $stBadge = '';
            if ($st === 'published') $stBadge = '<span class="badge" style="background:rgba(16,185,129,0.15);color:#34D399;font-size:0.75rem"><i class="bi bi-globe me-1"></i>Published</span>';
            elseif ($st === 'review') $stBadge = '<span class="badge" style="background:rgba(245,158,11,0.15);color:#FBBF24;font-size:0.75rem"><i class="bi bi-search me-1"></i>In Review</span>';
            else $stBadge = '<span class="badge" style="background:rgba(148,163,184,0.15);color:#94A3B8;font-size:0.75rem"><i class="bi bi-journal me-1"></i>Draft</span>';
            $row[] = '<div class="text-center">' . $stBadge . '</div>';
            
            // 4: Validations
            $valText = '';
            if ($content['validation_count'] > 0) {
                $avg = round($content['avg_score'], 1);
                $color = $avg >= 80 ? '#4ADE80' : ($avg >= 60 ? '#FBBF24' : '#F87171');
                $valText = '<div class="fw-bold" style="color:' . $color . '">' . $avg . '/100</div><div style="font-size:0.7rem;color:var(--text-muted)">' . $content['validation_count'] . ' validator</div>';
            } else {
                $valText = '<span style="font-size:0.8rem;color:var(--text-muted)">Belum ada</span>';
            }
            $row[] = '<div class="text-center">' . $valText . '</div>';
            
            // 5: Actions
            $editUrl = base_url("admin/contents/edit/{$content['id']}");
            $deleteUrl = base_url("admin/contents/delete/{$content['id']}");
            $csrf = csrf_field();
            $actionBtn = '
            <div class="d-flex gap-2 justify-content-center">
                <a href="'.$editUrl.'" class="btn btn-sm" style="background:rgba(37,99,235,0.1);color:#60A5FA;border:1px solid rgba(37,99,235,0.2);border-radius:0.5rem" title="Edit Data">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="'.$deleteUrl.'" method="post" onsubmit="return confirm(\'Yakin hapus materi ini?\')">
                    '.$csrf.'
                    <button type="submit" class="btn btn-sm" style="background:rgba(220,38,38,0.1);color:#F87171;border:1px solid rgba(220,38,38,0.2);border-radius:0.5rem" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>';
            $row[] = $actionBtn;

            $data[] = $row;
        }

        return $this->response->setJSON([
            'draw'            => $postData['draw'] ?? 1,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $data
        ]);
    }

    public function create(): string
    {
        return $this->renderView('admin/contents/form', [
            'title'   => 'Tambah Materi — SIREMAJA',
            'content' => null,
            'action'  => base_url('admin/contents/store'),
        ]);
    }

    public function store()
    {
        $rules = [
            'title'    => 'required|max_length[255]',
            'category' => 'required|in_list[hiv_dasar,penularan_pencegahan,perilaku_berisiko,umum]',
            'body'     => 'required|min_length[50]',
            'status'   => 'required|in_list[draft,review,published]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->contentModel->insert([
            'title'        => $this->request->getPost('title'),
            'category'     => $this->request->getPost('category'),
            'body'         => $this->request->getPost('body'),
            'media_url'    => $this->request->getPost('media_url') ?: null,
            'media_type'   => $this->request->getPost('media_type') ?? 'none',
            'status'       => $this->request->getPost('status'),
            'order_number' => (int)$this->request->getPost('order_number') ?: 99,
            'created_by'   => session()->get('user_id'),
        ]);

        $this->auditModel->log('CREATE', 'contents', $id);

        return redirect()->to(base_url('admin/contents'))->with('success', 'Materi berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $content = $this->contentModel->find($id);
        if (! $content) return redirect()->to(base_url('admin/contents'))->with('error', 'Materi tidak ditemukan.');

        return $this->renderView('admin/contents/form', [
            'title'   => 'Edit Materi — SIREMAJA',
            'content' => $content,
            'action'  => base_url("admin/contents/update/{$id}"),
        ]);
    }

    public function update(int $id)
    {
        $rules = [
            'title'    => 'required|max_length[255]',
            'category' => 'required|in_list[hiv_dasar,penularan_pencegahan,perilaku_berisiko,umum]',
            'body'     => 'required|min_length[50]',
            'status'   => 'required|in_list[draft,review,published]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldData = $this->contentModel->find($id);
        
        $newData = [
            'title'        => $this->request->getPost('title'),
            'category'     => $this->request->getPost('category'),
            'body'         => $this->request->getPost('body'),
            'media_url'    => $this->request->getPost('media_url') ?: null,
            'media_type'   => $this->request->getPost('media_type') ?? 'none',
            'status'       => $this->request->getPost('status'),
            'order_number' => (int)$this->request->getPost('order_number') ?: 99,
        ];
        
        $this->contentModel->update($id, $newData);

        $this->auditModel->log('UPDATE', 'contents', $id, $oldData, $newData);

        return redirect()->to(base_url('admin/contents'))->with('success', 'Materi berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $oldData = $this->contentModel->find($id);
        
        if ($oldData) {
            $this->contentModel->delete($id);
            $this->auditModel->log('DELETE', 'contents', $id, $oldData);
        }
        
        return redirect()->to(base_url('admin/contents'))->with('success', 'Materi berhasil dihapus.');
    }
}
