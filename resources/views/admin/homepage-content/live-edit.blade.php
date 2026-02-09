@extends('layouts.admin')

@section('title', 'แก้ไขเนื้อหาหน้าหลักแบบสด')
@section('page-title', 'แก้ไขเนื้อหาหน้าหลักแบบสด')

@push('styles')
    <style>
        .editable-content {
            position: relative;
            outline: 2px dashed rgba(255, 0, 0, 0.5);
            cursor: pointer;
            padding: 2px;
            margin: -2px; /* counteract padding for outline */
        }
        .editable-content:hover {
            outline-color: rgba(255, 0, 0, 0.8);
        }
        .editable-content.editing {
            outline: 2px solid red;
            background-color: rgba(255, 255, 0, 0.1);
        }
        .admin-edit-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            z-index: 10000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-edit-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none; /* Hidden by default */
        }
        .admin-edit-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 10001;
            width: 90%;
            max-width: 600px;
            display: none; /* Hidden by default */
        }
        /* Adjust body padding to account for toolbar */
        body {
            padding-top: 50px;
        }
    </style>
@endpush

@section('content')
    <div class="admin-edit-toolbar">
        <span class="text-lg font-bold">โหมดแก้ไขเนื้อหาหน้าหลัก</span>
        <button id="exitLiveEdit" class="btn btn-sm btn-warning">ออกจากโหมดแก้ไข</button>
    </div>

    <div id="admin-edit-overlay" class="admin-edit-overlay"></div>
    <div id="admin-edit-modal" class="admin-edit-modal">
        <h3 class="text-xl font-bold mb-4">แก้ไขเนื้อหา</h3>
        <input type="hidden" id="edit-content-id">
        <input type="hidden" id="edit-content-field">
        <input type="hidden" id="edit-content-type">

        <div class="mb-4">
            <label for="edit-textarea" class="block text-gray-700 text-sm font-bold mb-2">เนื้อหา:</label>
            <textarea id="edit-textarea" class="textarea textarea-bordered w-full" rows="10"></textarea>
            <input type="file" id="edit-file-input" class="file-input file-input-bordered w-full hidden mt-2">
            <input type="text" id="edit-link-input" class="input input-bordered w-full hidden mt-2" placeholder="Enter URL">
        </div>
        <div class="flex justify-end">
            <button id="saveChanges" class="btn btn-success mr-2">บันทึก</button>
            <button id="cancelEdit" class="btn btn-ghost">ยกเลิก</button>
        </div>
    </div>

    {{-- Include the actual homepage content --}}
    @include('index', ['recommendedProducts' => $recommendedProducts, 'homepageContents' => $homepageContents])
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const adminToolbar = document.querySelector('.admin-edit-toolbar');
            const editModal = document.getElementById('admin-edit-modal');
            const editOverlay = document.getElementById('admin-edit-overlay');
            const editContentId = document.getElementById('edit-content-id');
            const editContentField = document.getElementById('edit-content-field');
            const editContentType = document.getElementById('edit-content-type');
            const editTextArea = document.getElementById('edit-textarea');
            const editFileInput = document.getElementById('edit-file-input');
            const editLinkInput = document.getElementById('edit-link-input');
            const saveChangesBtn = document.getElementById('saveChanges');
            const cancelEditBtn = document.getElementById('cancelEdit');
            const exitLiveEditBtn = document.getElementById('exitLiveEdit');

            let currentEditableElement = null;

            // Adjust body padding for the toolbar
            document.body.style.paddingTop = adminToolbar.offsetHeight + 'px';

            // Helper to get nested value from an object
            function getNestedValue(obj, path) {
                return path.split('.').reduce((acc, part) => acc && acc[part] !== undefined ? acc[part] : undefined, obj);
            }

            function showEditor(item) {
                currentEditableElement = item.element;
                const id = item.id;
                const field = item.field; // e.g., 'value', 'data.title', 'data.image'
                const type = item.type; // e.g., 'text', 'image', 'collection'
                let valueToEdit = item.value; // Initial value extracted from DOM or data-*

                // Set hidden fields
                editContentId.value = id;
                editContentField.value = field;
                editContentType.value = type;

                // Reset all input fields
                editTextArea.classList.add('hidden');
                editFileInput.classList.add('hidden');
                editLinkInput.classList.add('hidden');
                editTextArea.value = '';
                editFileInput.value = '';
                editLinkInput.value = '';

                // Determine which input to show and its value
                if (type === 'image') {
                    editFileInput.classList.remove('hidden');
                    // For image, no direct value for file input
                } else if (type === 'link') {
                    editLinkInput.classList.remove('hidden');
                    editLinkInput.value = valueToEdit;
                } else { // text, icon, or data.field
                    editTextArea.classList.remove('hidden');
                    editTextArea.value = valueToEdit;
                }

                editModal.style.display = 'block';
                editOverlay.style.display = 'block';
                if (currentEditableElement) {
                    currentEditableElement.classList.add('editing');
                }
            }

            function hideEditor() {
                editModal.style.display = 'none';
                editOverlay.style.display = 'none';
                if (currentEditableElement) {
                    currentEditableElement.classList.remove('editing');
                    currentEditableElement = null;
                }
            }

            cancelEditBtn.addEventListener('click', hideEditor);
            editOverlay.addEventListener('click', hideEditor); // Close if click outside modal
            exitLiveEditBtn.addEventListener('click', function() {
                window.location.href = "{{ route('admin.homepage-content.index') }}"; // Redirect to admin panel
            });


            document.querySelectorAll('[data-homepage-content-id]').forEach(element => {
                element.classList.add('editable-content');

                // Prevent default right-click context menu
                element.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    const id = this.dataset.homepageContentId;
                    const field = this.dataset.homepageContentField; // e.g., 'value', 'data.title', 'data.image'
                    const type = this.dataset.homepageContentType; // e.g., 'text', 'image', 'collection'

                    let value;

                    // Extract the value based on field and type
                    if (type === 'image' && this.tagName === 'IMG') {
                        value = this.src;
                    } else if (type === 'link' && this.tagName === 'A') {
                        value = this.href;
                    } else if (field.startsWith('data.')) {
                        // For nested data fields, get the value from the current element's content
                        // or from its dataset.homepageContentData if available and necessary.
                        // For now, innerText is assumed for text/icon types.
                        // For more robust handling, 'dataset.homepageContentData' would be parsed
                        // and then 'getNestedValue' applied.
                        if (this.dataset.homepageContentData) {
                             try {
                                const fullData = JSON.parse(this.dataset.homepageContentData);
                                value = getNestedValue(fullData, field.substring(5));
                            } catch (error) {
                                console.error("Error parsing homepageContentData for nested field:", error);
                                value = this.innerText; // Fallback
                            }
                        } else {
                            value = this.innerText;
                        }
                    } else { // 'value' field (text, icon, or generic link/image path in 'value')
                        value = this.innerText;
                    }
                    showEditor({
                        element: this,
                        id: id,
                        field: field,
                        type: type,
                        value: value.trim() // Trim whitespace for text content
                    });
                });
            });

            saveChangesBtn.addEventListener('click', function() {
                const id = editContentId.value;
                const field = editContentField.value; // e.g., 'value', 'data.title'
                const type = editContentType.value; // e.g., 'text', 'image', 'collection'

                const formData = new FormData();
                formData.append('field', field);
                formData.append('_method', 'POST'); // Laravel expects POST for form submission, even if the route is logically PUT/PATCH

                if (type === 'image') {
                    const imageFile = editFileInput.files[0];
                    if (imageFile) {
                        formData.append('image_file', imageFile);
                    } else {
                        // If no file is selected, but it's an image field, it means no update
                        // or user intended to remove/clear the image (not currently supported simply).
                        alert('Please select an image file to upload or close the editor.');
                        return;
                    }
                } else if (type === 'link') {
                    formData.append('new_value', editLinkInput.value);
                } else { // 'text', 'icon', or nested field within 'data' like 'data.title'
                    formData.append('new_value', editTextArea.value);
                }

                fetch("{{ url('admin/homepage-content') }}/" + id + "/update-value", {
                        method: 'POST', // Always POST for FormData with file uploads
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Content updated successfully!');
                            if (currentEditableElement) {
                                if (type === 'image' && data.new_image_url) {
                                    currentEditableElement.src = data.new_image_url;
                                } else if (type === 'link') {
                                    currentEditableElement.href = editLinkInput.value;
                                } else if (field.startsWith('data.')) {
                                    // For nested data, update the specific text content or trigger a full reload for complex rendering
                                    if (currentEditableElement.tagName === 'IMG' && data.new_image_url) {
                                        currentEditableElement.src = data.new_image_url;
                                    } else if (currentEditableElement.tagName === 'A' && field.endsWith('.link')) {
                                        currentEditableElement.href = editLinkInput.value;
                                    } else {
                                        currentEditableElement.innerText = editTextArea.value;
                                    }
                                    // A full reload might still be necessary for collection types where multiple fields are inter-dependent
                                    // location.reload();
                                } else { // 'value' field (text, icon)
                                    currentEditableElement.innerText = editTextArea.value;
                                }
                            }
                            hideEditor();
                        } else {
                            alert('Error updating content: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error or server error.');
                    });
            });
        });
    </script>
@endpush
