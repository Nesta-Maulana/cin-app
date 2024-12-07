@extends('layouts.admin.app')
@section('title', 'Material')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Buat @yield('title')</small>
                    </div>
                    <a href="{{ route('material.index') }}" class="btn p-0" title="Kembali">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('material.store') }}" method="POST">
                        @csrf

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> Terdapat beberapa masalah dengan inputan Anda.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name (名称)</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Name (名称)" required autofocus />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name_mandarin" class="form-label">Name in Mandarin (中文名称)</label>
                                <input class="form-control" type="text" id="name_mandarin" name="name_mandarin"
                                    value="{{ old('name_mandarin') }}" placeholder="Name in Mandarin (中文名称)" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="code" class="form-label">Material Code (材料代码)</label>
                                <input class="form-control" type="text" id="code" name="code"
                                    value="{{ old('code',$code) }}" placeholder="Material Code (材料代码)" readonly required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="unit_id" class="form-label">Material Unit ID (单位 ID)</label>
                                <select class="form-control" id="unit_id" name="unit_id">
                                    <option value="">Select Unit (选择单位)</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                            {{ $unit->name_mandarin ? '(' . $unit->name_mandarin . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="price" class="form-label">Price (价格)</label>
                                <input class="form-control" type="text" id="price" name="price"
                                    value="{{ old('price') }}" placeholder="Price (价格)" required
                                    pattern="^\d+(\.\d{1,2})?$"
                                    title="Please enter a valid price with up to two decimal places.">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="description" class="form-label">Description (描述)</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Description (描述)">{{ old('description') }}</textarea>
                            </div>

                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                            <a href="{{ route('material.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
