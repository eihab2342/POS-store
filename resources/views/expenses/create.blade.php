@extends('layouts.app')

@section('title', 'إضافة مصروف جديد')

@section('content')
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #818cf8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .form-wrapper {
            max-width: 900px;
            margin: 2rem auto;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .form-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        }

        .header-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .header-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .form-body {
            padding: 3rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #475569;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 1rem;
            color: #334155;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 1rem;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 1rem;
            color: #334155;
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .file-upload-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.02);
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-icon i {
            font-size: 1.25rem;
            color: white;
        }

        .amount-wrapper {
            position: relative;
        }

        .currency {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-weight: 500;
        }

        .amount-input {
            padding-left: 3rem !important;
        }

        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-secondary {
            background: #64748b;
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-1px);
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .file-preview {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #dcfce7;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }

        .file-name {
            color: #16a34a;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .file-size {
            font-size: 0.75rem;
            color: #059669;
        }

        .hint-text {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .required::after {
            content: ' *';
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-body {
                padding: 2rem;
            }

            .form-header {
                padding: 2rem;
            }

            .header-title {
                font-size: 1.75rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>

    <div class="form-wrapper">
        <div class="form-card">
            <div class="form-header">
                <div class="header-content">
                    <h1 class="header-title">
                        <i class="fas fa-plus-circle ml-2"></i>
                        إضافة مصروف جديد
                    </h1>
                    <p class="header-subtitle">املأ التفاصيل أدناه لتسجيل مصروف جديد</p>
                </div>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="form-body">
                @csrf

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        المعلومات الأساسية
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="expense_date" class="form-label required">تاريخ المصروف</label>
                            <input type="date" name="expense_date" id="expense_date"
                                value="{{ old('expense_date', date('Y-m-d')) }}" class="form-input" required>
                            @error('expense_date')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount" class="form-label required">المبلغ (ج.م)</label>
                            <div class="amount-wrapper">
                                <span class="currency">ج.م</span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0"
                                    value="{{ old('amount') }}" placeholder="0.00" class="form-input amount-input" required>
                            </div>
                            @error('amount')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expense_type" class="form-label required">نوع المصروف</label>
                            <select name="expense_type" id="expense_type" class="form-select" required>
                                <option value="">اختر نوع المصروف</option>
                                @foreach(App\Models\Expense::getExpenseTypes() as $key => $label)
                                    <option value="{{ $key }}" {{ old('expense_type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_type')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_method" class="form-label required">طريقة الدفع</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">اختر طريقة الدفع</option>
                                @foreach(App\Models\Expense::getPaymentMethods() as $key => $label)
                                    <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-users"></i>
                        معلومات إضافية
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="supplier_id" class="form-label">المورد</label>
                            <select name="supplier_id" id="supplier_id" class="form-select">
                                <option value="">اختر المورد (اختياري)</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="employee_id" class="form-label">الموظف المسؤول</label>
                            <select name="employee_id" id="employee_id" class="form-select">
                                <option value="">اختر الموظف (اختياري)</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reference_number" class="form-label">رقم المرجع</label>
                        <input type="text" name="reference_number" id="reference_number"
                            value="{{ old('reference_number') }}" placeholder="رقم الفاتورة أو المرجع..."
                            class="form-input">
                        @error('reference_number')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-file-alt"></i>
                        الوصف والملاحظات
                    </h3>

                    <div class="form-group">
                        <label for="description" class="form-label required">وصف المصروف</label>
                        <textarea name="description" id="description" class="form-textarea"
                            placeholder="صف المصروف بالتفصيل..." rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">ملاحظات إضافية</label>
                        <textarea name="notes" id="notes" class="form-textarea" placeholder="ملاحظات إضافية (اختياري)..."
                            rows="2">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-paperclip"></i>
                        المرفقات
                    </h3>

                    <div class="form-group">
                        <div class="file-upload-wrapper" id="fileUploadContainer">
                            <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.pdf"
                                class="file-input">
                            <label for="attachment" class="file-label">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div>
                                    <p class="text-gray-700 font-medium">رفع ملف</p>
                                    <p class="text-gray-500 text-sm">اسحب وأسقط الملف هنا أو انقر للاختيار</p>
                                </div>
                            </label>
                        </div>
                        <div id="filePreview"></div>
                        <p class="hint-text">
                            <i class="fas fa-info-circle ml-1"></i>
                            يمكن رفع صورة الفاتورة أو PDF (الحد الأقصى 2MB)
                        </p>
                        @error('attachment')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="btn-group">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right ml-1"></i>
                        رجوع
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        حفظ المصروف
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.addEventListener('blur', function () {
                    if (this.value) {
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                });
            }

            const employeeSelect = document.getElementById('employee_id');
            if (employeeSelect && {{ auth()->id() }}) {
                const option = document.createElement('option');
                option.value = {{ auth()->id() }};
                option.text = '{{ auth()->user()->name }} (أنا)';
                employeeSelect.appendChild(option);
            }

            const fileInput = document.getElementById('attachment');
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const filePreview = document.getElementById('filePreview');

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    updateFilePreview(this.files[0]);
                });
            }

            if (fileUploadContainer) {
                fileUploadContainer.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    this.style.borderColor = '#4f46e5';
                    this.style.background = 'rgba(79, 70, 229, 0.05)';
                });

                fileUploadContainer.addEventListener('dragleave', function () {
                    this.style.borderColor = '#cbd5e1';
                    this.style.background = '#f8fafc';
                });

                fileUploadContainer.addEventListener('drop', function (e) {
                    e.preventDefault();
                    this.style.borderColor = '#cbd5e1';
                    this.style.background = '#f8fafc';

                    if (e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        updateFilePreview(e.dataTransfer.files[0]);
                    }
                });
            }

            function updateFilePreview(file) {
                if (!file) return;

                const fileSize = (file.size / (1024 * 1024)).toFixed(2);

                if (fileSize > 2) {
                    alert('حجم الملف كبير جداً. الحد الأقصى 2MB');
                    fileInput.value = '';
                    filePreview.innerHTML = '';
                    return;
                }

                filePreview.innerHTML = `
                <div class="file-preview">
                    <div class="file-name">
                        <i class="fas fa-file"></i>
                        ${file.name}
                    </div>
                    <div class="file-size">
                        <i class="fas fa-hdd ml-1"></i>
                        الحجم: ${fileSize} ميجابايت
                    </div>
                </div>
            `;
            }

            const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
            formInputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.style.transform = 'translateY(-1px)';
                });

                input.addEventListener('blur', function () {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endsection