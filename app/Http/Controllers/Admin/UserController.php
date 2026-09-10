<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $this->ensureCanManage($user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureCanManage($user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($user->isProtected()) {
            $data['email'] = User::PROTECTED_EMAIL;
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $this->ensureCanManage($user);

        if ($user->id === auth()->id() && ! $user->isProtected()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'Você não pode excluir o próprio usuário logado.']);
        }

        if (User::query()->count() <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'É necessário manter ao menos um usuário admin.']);
        }

        $wasSelf = $user->id === auth()->id();
        $user->delete();

        if ($wasSelf) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->with('success', 'Usuário excluído. Sessão encerrada.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    private function ensureCanManage(User $user): void
    {
        abort_unless($user->canBeManagedBy(auth()->user()), 403, 'Este usuário protegido só pode ser gerenciado por ele mesmo.');
    }
}
