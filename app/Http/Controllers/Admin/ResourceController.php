<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Consultation;
use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Rule;
use App\Models\Symptom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;

class ResourceController extends Controller
{
    private array $resources = [
        'gejala' => [
            'title' => 'Data Gejala', 'model' => Symptom::class, 'order' => 'code',
            'fields' => ['code', 'name', 'category', 'description', 'duration', 'body_location', 'frequency', 'weight', 'is_active'],
            'columns' => ['code' => 'Kode', 'name' => 'Gejala', 'category' => 'Kategori', 'weight' => 'Bobot', 'is_active' => 'Aktif'],
        ],
        'penyakit' => [
            'title' => 'Data Penyakit', 'model' => Disease::class, 'order' => 'code',
            'fields' => ['code', 'name', 'severity', 'description', 'solution', 'is_active'],
            'columns' => ['code' => 'Kode', 'name' => 'Penyakit', 'severity' => 'Keparahan', 'is_active' => 'Aktif'],
        ],
        'obat' => [
            'title' => 'Data Obat', 'model' => Medicine::class, 'order' => 'code',
            'fields' => ['code', 'disease_id', 'name', 'category', 'dosage', 'usage_rule', 'side_effects', 'contraindication', 'warning', 'description', 'image_path', 'is_active'],
            'columns' => ['code' => 'Kode', 'name' => 'Obat', 'category' => 'Kategori', 'dosage' => 'Dosis', 'is_active' => 'Aktif'],
        ],
        'rule' => [
            'title' => 'Data Rule', 'model' => Rule::class, 'order' => 'code',
            'fields' => ['code', 'disease_id', 'symptom_codes', 'medicine_codes', 'cf_value', 'method', 'description', 'is_active'],
            'columns' => ['code' => 'Kode', 'disease_id' => 'Penyakit', 'symptom_codes' => 'IF Gejala', 'medicine_codes' => 'Output Obat', 'cf_value' => 'CF'],
        ],
        'user' => [
            'title' => 'Data User', 'model' => User::class, 'order' => 'name',
            'fields' => ['name', 'username', 'email', 'role', 'gender', 'phone', 'address', 'password'],
            'columns' => ['name' => 'Nama', 'username' => 'Username', 'email' => 'Email', 'role' => 'Role', 'phone' => 'No HP'],
        ],
        'riwayat' => [
            'title' => 'Riwayat Konsultasi', 'model' => Consultation::class, 'order' => 'created_at',
            'fields' => ['status', 'notes'],
            'columns' => ['created_at' => 'Tanggal', 'user_id' => 'User', 'disease_id' => 'Penyakit', 'confidence_score' => 'CF', 'status' => 'Status'],
            'readonly' => true,
        ],
        'pengaturan' => [
            'title' => 'Pengaturan', 'model' => AppSetting::class, 'order' => 'key',
            'fields' => ['key', 'value', 'group'],
            'columns' => ['key' => 'Key', 'value' => 'Value', 'group' => 'Group'],
        ],
    ];

    public function index(string $resource)
    {
        $config = $this->config($resource);
        $query = $config['model']::query();
        if ($resource === 'riwayat') {
            $query->with(['user', 'disease'])->latest();
        } elseif (in_array($resource, ['obat', 'rule'], true)) {
            $query->with('disease')->orderBy($config['order'] ?? 'id');
        } else {
            $query->orderBy($config['order'] ?? 'id');
        }

        if ($search = request('q')) {
            $query->where(function ($inner) use ($search, $config) {
                foreach (array_keys($config['columns']) as $column) {
                    if (in_array($column, ['code', 'name', 'severity', 'status', 'email', 'username'], true)) {
                        $inner->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        if ($category = request('category')) {
            if (in_array('category', $config['fields'] ?? [], true)) {
                $query->where('category', $category);
            }
        }

        return view('admin.resource.index', [
            'resource' => $resource,
            'config' => $config,
            'items' => $query->paginate(12)->withQueryString(),
            'diseases' => Disease::orderBy('code')->get(),
        ]);
    }

    public function create(string $resource)
    {
        $config = $this->config($resource);
        abort_if(($config['readonly'] ?? false), 403);
        return view('admin.resource.form', ['resource' => $resource, 'config' => $config, 'item' => null, 'diseases' => Disease::orderBy('code')->get()]);
    }

    public function store(Request $request, string $resource)
    {
        $config = $this->config($resource);
        abort_if(($config['readonly'] ?? false), 403);
        $data = $this->validatedData($request, $resource);
        $config['model']::create($data);
        return redirect()->route('admin.resource.index', $resource)->with('success', $config['title'].' berhasil ditambahkan.');
    }

    public function edit(string $resource, int $id)
    {
        $config = $this->config($resource);
        abort_if(($config['readonly'] ?? false), 403);
        $item = $config['model']::findOrFail($id);
        return view('admin.resource.form', compact('resource', 'config', 'item') + ['diseases' => Disease::orderBy('code')->get()]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $config = $this->config($resource);
        abort_if(($config['readonly'] ?? false), 403);
        $item = $config['model']::findOrFail($id);
        $data = $this->validatedData($request, $resource, $id);
        $item->update($data);
        return redirect()->route('admin.resource.index', $resource)->with('success', $config['title'].' berhasil diperbarui.');
    }

    public function destroy(string $resource, int $id)
    {
        $config = $this->config($resource);
        $item = $config['model']::findOrFail($id);
        $item->delete();
        return back()->with('success', $config['title'].' berhasil dihapus.');
    }

    private function config(string $resource): array
    {
        abort_unless(isset($this->resources[$resource]), 404);
        return $this->resources[$resource];
    }

    private function validatedData(Request $request, string $resource, ?int $id = null): array
    {
        return match ($resource) {
            'gejala' => $this->validateSymptom($request, $id),
            'penyakit' => $this->validateDisease($request, $id),
            'obat' => $this->validateMedicine($request, $id),
            'rule' => $this->validateRule($request, $id),
            'user' => $this->validateUser($request, $id),
            'pengaturan' => $request->validate(['key' => ['required', 'string', 'max:100'], 'value' => ['nullable', 'string'], 'group' => ['nullable', 'string', 'max:50']]),
            default => [],
        };
    }

    private function validateSymptom(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:10', ValidationRule::unique('symptoms', 'code')->ignore($id)],
            'name' => ['required', 'max:120'], 'category' => ['nullable', 'max:60'], 'description' => ['nullable'],
            'duration' => ['nullable', 'max:60'], 'body_location' => ['nullable', 'max:60'], 'frequency' => ['nullable', 'max:60'],
            'weight' => ['required', 'numeric', 'between:0,1'], 'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function validateDisease(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:10', ValidationRule::unique('diseases', 'code')->ignore($id)],
            'name' => ['required', 'max:120'], 'severity' => ['nullable', 'max:40'], 'description' => ['nullable'], 'solution' => ['nullable'], 'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function validateMedicine(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:10', ValidationRule::unique('medicines', 'code')->ignore($id)],
            'disease_id' => ['nullable', 'exists:diseases,id'], 'name' => ['required', 'max:160'], 'category' => ['nullable', 'max:80'],
            'dosage' => ['nullable', 'max:120'], 'usage_rule' => ['nullable'], 'side_effects' => ['nullable'], 'contraindication' => ['nullable'],
            'warning' => ['nullable'], 'description' => ['nullable'], 'image_path' => ['nullable', 'max:255'], 'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function validateRule(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:10', ValidationRule::unique('rules', 'code')->ignore($id)],
            'disease_id' => ['required', 'exists:diseases,id'], 'symptom_codes' => ['required'], 'medicine_codes' => ['required'],
            'cf_value' => ['required', 'numeric', 'between:0,1'], 'method' => ['required', 'in:parallel'],
            'description' => ['nullable'], 'is_active' => ['nullable', 'boolean'],
        ]);
        $data['symptom_codes'] = $this->splitCodes($data['symptom_codes']);
        $data['medicine_codes'] = $this->splitCodes($data['medicine_codes']);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function validateUser(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'name' => ['required', 'max:120'], 'username' => ['required', 'max:60', ValidationRule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', 'max:120', ValidationRule::unique('users', 'email')->ignore($id)],
            'role' => ['required', 'in:admin,masyarakat'], 'gender' => ['nullable', 'max:20'], 'phone' => ['nullable', 'max:30'],
            'address' => ['nullable'], 'password' => [$id ? 'nullable' : 'required', 'min:6'],
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }

    private function splitCodes(string|array $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        return array_values(array_filter(array_map(fn ($v) => strtoupper(trim($v)), preg_split('/[,;\s]+/', $value))));
    }
}
