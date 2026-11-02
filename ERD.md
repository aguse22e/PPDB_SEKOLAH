# ERD PPDB - Ringkas

```
roles 1---N users 1---1 applicants 1---N guardians
                              |
                              1
                              |
                              N
academic_years 1---N admission_periods 1---N applications N---1 admission_paths
                              |                    |  \
                              N                    |   N---1 majors (pilihan 1 & 2)
                       period_major_quotas         |
                              N                    +---N application_documents N---1 document_types
                              |                    +---N payments
                        majors                     +---N selection_results

admission_paths N---N document_types
  (lewat tabel pivot: path_document_requirements)

users 1---N notifications
users 1---N audit_logs
admission_periods 1---N announcements
```

## Aturan penting
1. File dokumen TIDAK disimpan di database. Simpan file di storage private, database hanya menyimpan `file_path`.
2. Pisahkan status: `applications.status`, `application_documents.verification_status`, `payments.status`.
3. Syarat dokumen ditentukan dari `path_document_requirements`, jadi admin bisa mengubah syarat per jalur tanpa mengubah kode.
4. Kuota jurusan per gelombang diatur di `period_major_quotas`, bukan hanya di `majors.quota`.
5. NIK / nomor KK termasuk data sensitif: batasi akses per role, catat di `audit_logs`, jangan tampilkan penuh di laporan publik.
