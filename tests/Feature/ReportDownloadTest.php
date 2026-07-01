<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_preview_and_download_own_history_report(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();
        $this->createConsultationFor($user);

        $this->actingAs($user)
            ->get(route('reports.user.history.preview'))
            ->assertOk()
            ->assertSee('Laporan Riwayat Konsultasi', false)
            ->assertSee('User Masyarakat', false);

        $this->actingAs($user)
            ->get(route('reports.user.history.download', ['format' => 'json']))
            ->assertOk()
            ->assertHeader('content-disposition')
            ->assertJsonPath('report.title', 'Laporan Riwayat Konsultasi');

        $this->actingAs($user)
            ->get(route('reports.user.history.download', ['format' => 'csv']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertSee('Tanggal');

        $this->actingAs($user)
            ->get(route('reports.user.history.download', ['format' => 'pdf']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('history.index'))
            ->assertOk()
            ->assertSee('id="user-report-modal"', false)
            ->assertSee('data-report-modal', false)
            ->assertSee(route('reports.user.history.preview'), false);
    }

    public function test_admin_can_preview_and_download_complete_dashboard_report(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $user = User::where('role', 'masyarakat')->firstOrFail();
        $this->createConsultationFor($user);

        $this->actingAs($admin)
            ->get(route('admin.reports.dashboard.preview'))
            ->assertOk()
            ->assertSee('Laporan Semua Data Sistem', false)
            ->assertSee('Data Gejala', false)
            ->assertSee('Data Riwayat Konsultasi', false);

        $this->actingAs($admin)
            ->get(route('admin.reports.dashboard.download', ['format' => 'excel']))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8')
            ->assertSee('Data Obat', false);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('id="admin-report-modal"', false)
            ->assertSee('data-report-modal', false)
            ->assertSee(route('admin.reports.dashboard.preview'), false);
    }

    public function test_report_routes_are_role_limited(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.reports.dashboard.preview'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('reports.user.history.preview'))
            ->assertForbidden();
    }

    private function createConsultationFor(User $user): Consultation
    {
        $this->actingAs($user)->post(route('consultation.diagnose'), [
            'symptoms' => ['G001', 'G009', 'G011'],
            'notes' => 'Demam dan pilek sejak pagi',
        ]);

        return Consultation::where('user_id', $user->id)->firstOrFail();
    }
}
