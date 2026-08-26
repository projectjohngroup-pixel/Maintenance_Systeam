<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manajemen User</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--pds-soft);
            color: var(--pds-ink);
        }

        .container {
            max-width: 1200px;
            margin: 35px auto;
            padding: 20px;
        }

        .back {
            display: inline-block;
            padding: 10px 15px;
            background: var(--pds-line);
            color: var(--pds-ink-2);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .back:hover {
            background: var(--pds-line-2);
        }

        .card {
            background: var(--pds-card);
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
            margin-bottom: 20px;
        }

        h1 {
            font-size: 25px;
            margin-bottom: 6px;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .subtitle {
            color: var(--pds-muted);
            font-size: 14px;
            margin-bottom: 22px;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input,
        select {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid var(--pds-line-2);
            border-radius: 8px;
            font-size: 14px;
            background: var(--pds-card);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 9px 13px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-gray {
            background: var(--pds-line);
            color: var(--pds-ink-2);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
        }

        .section-description {
            color: var(--pds-muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .bagian-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .bagian-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 11px;
            border: 1px solid var(--pds-line);
            border-radius: 8px;
            background: var(--pds-soft);
            font-size: 13px;
        }

        .bagian-actions {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .icon-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 15px;
            padding: 2px 4px;
            line-height: 1;
        }

        .edit-icon {
            color: #2563eb;
        }

        .delete-icon {
            color: #dc2626;
        }

        .add-box {
            display: none;
            margin-bottom: 18px;
            padding: 15px;
            background: var(--pds-soft);
            border: 1px solid var(--pds-line);
            border-radius: 10px;
        }

        .add-box.active {
            display: block;
        }

        .edit-box {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: var(--pds-soft);
            border-radius: 9px;
            border: 1px solid var(--pds-line);
        }

        .edit-box.active {
            display: block;
        }

        .empty-text {
            color: var(--pds-muted-2);
            font-size: 13px;
            padding: 8px 0;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 11px;
            border-bottom: 1px solid var(--pds-line);
            text-align: left;
            vertical-align: middle;
            font-size: 13px;
        }

        th {
            background: var(--pds-soft);
        }

        .status-active {
            color: #047857;
            font-weight: bold;
        }

        .status-inactive {
            color: #b91c1c;
            font-weight: bold;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }

        .role-administrator {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .role-manager {
            background: #ede9fe;
            color: #6d28d9;
        }

        .role-direktur {
            background: #fce7f3;
            color: #9d174d;
        }

        .role-produksi {
            background: #dcfce7;
            color: #166534;
        }

        .role-maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .role-mekanik {
            background: #e0f2fe;
            color: #0369a1;
        }

        .role-prev-maint {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .role-other {
            background: var(--pds-line);
            color: var(--pds-ink-2);
        }

        .action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, .45);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-card {
            width: 430px;
            max-width: calc(100vw - 32px);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            background: var(--pds-card);
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .2);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .modal-header h3 {
            font-size: 18px;
        }

        .close-btn {
            border: none;
            background: transparent;
            font-size: 22px;
            cursor: pointer;
            color: var(--pds-muted);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        @media (max-width: 1000px) {

            .grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 650px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .container {
                margin: 20px auto;
                padding: 12px;
            }

            .card {
                padding: 20px;
            }

            .section-header {
                align-items: flex-start;
                flex-direction: column;
            }

        }

    </style>

</head>


<body>

<div class="container">


    {{-- =====================================================
        KEMBALI
    ====================================================== --}}

    <a
        href="{{ route('dashboard') }}"
        class="back"
    >
        <x-icon name="back"></x-icon> Kembali ke Dashboard
    </a>


    {{-- =====================================================
        PESAN
    ====================================================== --}}

    @if(session('success'))

        <div class="message success">
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div class="message error">
            {{ $errors->first() }}
        </div>

    @endif


    {{-- =====================================================
        ROLE SISTEM
    ====================================================== --}}

    <div class="card">

        <div class="section-header">

            <div>

                <div class="section-title">
                    Role Sistem
                </div>

                <div class="section-description">
                    Kelola Role Sistem dan batas akses pengguna.
                </div>

            </div>


            <button
                type="button"
                class="btn btn-primary"
                onclick="openSystemRoleForm()"
            >
                + Tambahkan
            </button>

        </div>


        {{-- FORM TAMBAH ROLE --}}

        <div
            id="systemRoleForm"
            class="add-box"
        >

            <form
                action="{{ route('users.system-role.store') }}"
                method="POST"
            >

                @csrf

                <div class="group">

                    <label for="system_role">
                        Nama Role Sistem
                    </label>

                    <input
                        type="text"
                        id="system_role"
                        name="system_role"
                        placeholder="Contoh: Supervisor"
                        required
                    >

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="btn btn-gray"
                        onclick="closeSystemRoleForm()"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>


        {{-- DAFTAR ROLE --}}

        <div class="bagian-list">

            @forelse($systemRoles as $systemRole)

                <div class="bagian-item">

                    <span>
                        {{ $systemRole }}
                    </span>


                    <div class="bagian-actions">

                        {{-- EDIT --}}

                        <button
                            type="button"
                            class="icon-btn edit-icon"
                            title="Edit Role"
                            onclick="openSystemRoleEdit(@js($systemRole))"
                        >
                            <x-icon name="edit"></x-icon>
                        </button>


                        {{-- HAPUS --}}

                        @if(
                            !in_array(
                                $systemRole,
                                [
                                    'Administrator',
                                    'Manager',
                                    'Direktur',
                                    'Produksi',
                                    'Maintenance',
                                    'Prev-Maint',
                                    'Mekanik / Maintenance',
                                ],
                                true
                            )
                        )

                            <form
                                action="{{ route(
                                    'users.system-role.delete',
                                    ['role' => $systemRole]
                                ) }}"
                                method="POST"
                                style="display:inline;"
                                data-confirm="Hapus Role Sistem {{ $systemRole }}?"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="icon-btn delete-icon"
                                    title="Hapus Role"
                                >
                                    <x-icon name="trash"></x-icon>
                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            @empty

                <div class="empty-text">
                    Belum ada Role Sistem.
                </div>

            @endforelse

        </div>


    </div>


    {{-- =====================================================
        TAMBAH USER
    ====================================================== --}}

    <div class="card">

        <h1>
            Manajemen User
        </h1>

        <p class="subtitle">
            Tambah, ubah, aktifkan, nonaktifkan, dan hapus user.
        </p>


        <form
            action="{{ route('users.store') }}"
            method="POST"
        >

            @csrf

            <div class="grid">


                {{-- ROLE SISTEM --}}

                <div class="group">

                    <label for="role">
                        Role Sistem
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                    >

                        <option value="">
                            -- Pilih Role --
                        </option>

                        @foreach($systemRoles as $systemRole)

                            <option
                                value="{{ $systemRole }}"
                                @selected(old('role') === $systemRole)
                            >
                                {{ $systemRole }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BAGIAN --}}

                <div class="group">

                    <label for="bagian">
                        Bagian
                    </label>

                    <select
                        id="bagian"
                        name="bagian"
                        required
                    >

                        <option value="">
                            -- Pilih Bagian --
                        </option>

                        @foreach($roles as $bagian)

                            <option
                                value="{{ $bagian }}"
                                @selected(old('bagian') === $bagian)
                            >
                                {{ $bagian }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- NAMA --}}

                <div class="group">

                    <label for="name">
                        Nama
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Nama user"
                        required
                    >

                </div>


                {{-- PASSWORD --}}

                <div class="group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                        placeholder="Minimal 6 karakter"
                        required
                    >

                </div>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                + Tambah User
            </button>

        </form>

    </div>


    {{-- =====================================================
        BAGIAN
    ====================================================== --}}

    <div class="card">

        <div class="section-header">

            <div>

                <div class="section-title">
                    Bagian
                </div>

                <div class="section-description">
                    Kelola daftar Bagian pengguna.
                </div>

            </div>

        </div>


        <form
            action="{{ route('users.role.store') }}"
            method="POST"
        >

            @csrf

            <div class="group">

                <label for="bagian_baru">
                    Tambah Bagian
                </label>

                <input
                    type="text"
                    id="bagian_baru"
                    name="role"
                    placeholder="Contoh: Gudang"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                + Tambah Bagian
            </button>

        </form>


        <div
            class="bagian-list"
            style="margin-top:18px;"
        >

            @forelse($roles as $bagian)

                <div class="bagian-item">

                    <span>
                        {{ $bagian }}
                    </span>


                    <div class="bagian-actions">

                        {{-- EDIT BAGIAN --}}

                        <button
                            type="button"
                            class="icon-btn edit-icon"
                            title="Edit Bagian"
                            onclick="openBagianEdit(@js($bagian))"
                        >
                            <x-icon name="edit"></x-icon>
                        </button>


                        {{-- HAPUS BAGIAN --}}

                        @if(
                            !\App\Models\User::where(
                                'bagian',
                                $bagian
                            )->exists()
                        )

                            <form
                                action="{{ route(
                                    'users.role.delete',
                                    ['role' => $bagian]
                                ) }}"
                                method="POST"
                                style="display:inline;"
                                data-confirm="Hapus Bagian {{ $bagian }}?"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="icon-btn delete-icon"
                                    title="Hapus Bagian"
                                >
                                    <x-icon name="trash"></x-icon>
                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            @empty

                <div class="empty-text">
                    Belum ada Bagian.
                </div>

            @endforelse

        </div>


        {{-- EDIT BAGIAN --}}

        <div
            id="bagianEditBox"
            class="edit-box"
        >

            <form
                id="bagianEditForm"
                method="POST"
            >

                @csrf

                @method('PATCH')


                <div class="group">

                    <label for="newBagian">
                        Nama Bagian Baru
                    </label>

                    <input
                        type="text"
                        id="newBagian"
                        name="new_role"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan
                </button>


                <button
                    type="button"
                    class="btn btn-gray"
                    onclick="closeBagianEdit()"
                >
                    Batal
                </button>

            </form>

        </div>

    </div>


    {{-- =====================================================
        DAFTAR USER
    ====================================================== --}}

    <div class="card">

        <h2>
            Daftar User
        </h2>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Nama
                        </th>

                        <th>
                            Role Sistem
                        </th>

                        <th>
                            Bagian
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Login Terakhir
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($users as $index => $user)

                        <tr>

                            {{-- NO --}}

                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- NAMA --}}

                            <td>
                                {{ $user->name }}
                            </td>


                            {{-- ROLE --}}

                            <td>

                                @php
                                    $roleKey = \App\Support\DepartmentAccess::normalizeRole($user->role ?? '');
                                    $roleLabel = \App\Support\DepartmentAccess::roleLabel($user->role ?? '');
                                @endphp

                                @if($roleKey === \App\Support\DepartmentAccess::ADMINISTRATOR)

                                    <span class="role-badge role-administrator">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($roleKey === \App\Support\DepartmentAccess::MANAGER)

                                    <span class="role-badge role-manager">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($roleKey === \App\Support\DepartmentAccess::DIREKTUR)

                                    <span class="role-badge role-direktur">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($roleKey === \App\Support\DepartmentAccess::PRODUKSI)

                                    <span class="role-badge role-produksi">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($roleKey === \App\Support\DepartmentAccess::MAINTENANCE)

                                    <span class="role-badge role-maintenance">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($roleKey === \App\Support\DepartmentAccess::MEKANIK_MAINT)

                                    <span class="role-badge role-mekanik">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($roleKey === \App\Support\DepartmentAccess::PREV_MAINT)

                                    <span class="role-badge role-prev-maint">
                                        {{ $roleLabel }}
                                    </span>

                                @elseif($user->role)

                                    <span class="role-badge role-other">
                                        {{ $user->role }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- BAGIAN --}}

                            <td>
                                {{ $user->bagian ?? '-' }}
                            </td>


                            {{-- STATUS --}}

                            <td>

                                @if($user->status === 'AKTIF')

                                    <span class="status-active">
                                        AKTIF
                                    </span>

                                @else

                                    <span class="status-inactive">
                                        NONAKTIF
                                    </span>

                                @endif

                            </td>


                            {{-- LOGIN TERAKHIR --}}

                            <td>
                                {{ $user->last_login_at ?? '-' }}
                            </td>


                            {{-- AKSI --}}

                            <td>

                                <div class="action-group">

                                    <button
                                        type="button"
                                        class="btn btn-warning"
                                        onclick="openUserEdit({{ $user->id }})"
                                    >
                                        Edit
                                    </button>


                                    <form
                                        action="{{ route(
                                            'users.status',
                                            $user->id
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-gray"
                                        >

                                            @if($user->status === 'AKTIF')
                                                Nonaktifkan
                                            @else
                                                Aktifkan
                                            @endif

                                        </button>

                                    </form>


                                    @if(auth()->id() !== $user->id)

                                        <form
                                            action="{{ route(
                                                'users.destroy',
                                                $user->id
                                            ) }}"
                                            method="POST"
                                            data-confirm="Hapus user {{ $user->name }}?"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                        {{-- =================================================
                            EDIT USER
                        ================================================== --}}

                        <tr
                            id="editUser{{ $user->id }}"
                            style="display:none;"
                        >

                            <td colspan="7">

                                <div class="edit-box active">

                                    <form
                                        action="{{ route(
                                            'users.update',
                                            $user->id
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <div class="grid">


                                            {{-- ROLE SISTEM --}}

                                            <div class="group">

                                                <label>
                                                    Role Sistem
                                                </label>

                                                <select
                                                    name="role"
                                                    required
                                                >

                                                    @foreach($systemRoles as $systemRole)

                                                        <option
                                                            value="{{ $systemRole }}"
                                                            @selected(
                                                                $systemRole === $user->role
                                                            )
                                                        >
                                                            {{ $systemRole }}
                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>


                                            {{-- BAGIAN --}}

                                            <div class="group">

                                                <label>
                                                    Bagian
                                                </label>

                                                <select
                                                    name="bagian"
                                                    required
                                                >

                                                    @foreach($roles as $bagian)

                                                        <option
                                                            value="{{ $bagian }}"
                                                            @selected(
                                                                $bagian === $user->bagian
                                                            )
                                                        >
                                                            {{ $bagian }}
                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>


                                            {{-- NAMA --}}

                                            <div class="group">

                                                <label>
                                                    Nama
                                                </label>

                                                <input
                                                    type="text"
                                                    name="name"
                                                    value="{{ $user->name }}"
                                                    required
                                                >

                                            </div>


                                            {{-- PASSWORD --}}

                                            <div class="group">

                                                <label>
                                                    Password Baru
                                                </label>

                                                <input
                                                    type="password"
                                                    name="password"
                                                    placeholder="Kosongkan jika tidak diubah"
                                                >

                                            </div>

                                        </div>


                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            Simpan
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-gray"
                                            onclick="closeUserEdit({{ $user->id }})"
                                        >
                                            Batal
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                style="
                                    text-align:center;
                                    color:var(--pds-muted-2);
                                    padding:30px;
                                "
                            >
                                Belum ada user.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
    MODAL EDIT ROLE SISTEM
========================================================= --}}

<div
    id="systemRoleEditModal"
    class="modal"
>

    <div class="modal-card">

        <div class="modal-header">

            <h3>
                Edit Role Sistem
            </h3>

            <button
                type="button"
                class="close-btn"
                onclick="closeSystemRoleEdit()"
            >
                ×
            </button>

        </div>


        <form
            id="systemRoleEditForm"
            method="POST"
        >

            @csrf

            @method('PATCH')


            <div class="group">

                <label for="editSystemRoleName">
                    Nama Role
                </label>

                <input
                    type="text"
                    id="editSystemRoleName"
                    name="new_role"
                    required
                >

            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    class="btn btn-gray"
                    onclick="closeSystemRoleEdit()"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
    SCRIPT
========================================================= --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | TAMBAH ROLE SISTEM
    |--------------------------------------------------------------------------
    */

    function openSystemRoleForm()
    {
        const box =
            document.getElementById(
                'systemRoleForm'
            );

        if (box) {

            box.classList.add(
                'active'
            );

            const input =
                document.getElementById(
                    'system_role'
                );

            if (input) {
                input.focus();
            }

        }
    }


    function closeSystemRoleForm()
    {
        const box =
            document.getElementById(
                'systemRoleForm'
            );

        if (box) {
            box.classList.remove(
                'active'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT ROLE SISTEM
    |--------------------------------------------------------------------------
    */

    function openSystemRoleEdit(role)
    {
        const modal =
            document.getElementById(
                'systemRoleEditModal'
            );

        const form =
            document.getElementById(
                'systemRoleEditForm'
            );

        const input =
            document.getElementById(
                'editSystemRoleName'
            );


        form.action =
            '/manajemen-user/system-role/' +
            encodeURIComponent(role);


        input.value =
            role;


        modal.classList.add(
            'active'
        );


        input.focus();
    }


    function closeSystemRoleEdit()
    {
        const modal =
            document.getElementById(
                'systemRoleEditModal'
            );

        if (modal) {

            modal.classList.remove(
                'active'
            );

        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT BAGIAN
    |--------------------------------------------------------------------------
    */

    function openBagianEdit(bagian)
    {
        const box =
            document.getElementById(
                'bagianEditBox'
            );

        const form =
            document.getElementById(
                'bagianEditForm'
            );

        const input =
            document.getElementById(
                'newBagian'
            );


        form.action =
            '/manajemen-user/role/' +
            encodeURIComponent(bagian);


        input.value =
            bagian;


        box.classList.add(
            'active'
        );


        box.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }


    function closeBagianEdit()
    {
        const box =
            document.getElementById(
                'bagianEditBox'
            );

        if (box) {

            box.classList.remove(
                'active'
            );

        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT USER
    |--------------------------------------------------------------------------
    */

    function openUserEdit(id)
    {
        const row =
            document.getElementById(
                'editUser' + id
            );

        if (row) {

            row.style.display =
                'table-row';

            row.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        }
    }


    function closeUserEdit(id)
    {
        const row =
            document.getElementById(
                'editUser' + id
            );

        if (row) {

            row.style.display =
                'none';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL SAAT KLIK LUAR
    |--------------------------------------------------------------------------
    */

    document
        .getElementById(
            'systemRoleEditModal'
        )
        .addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    this
                ) {
                    closeSystemRoleEdit();
                }

            }
        );

</script>


</body>

</html>