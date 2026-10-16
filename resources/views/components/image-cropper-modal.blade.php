<!-- Global Image Cropper Modal -->
<div id="cropper-modal" style="display: none;" class="fixed inset-0 z-100 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4" onclick="if(event.target === this) closeCropperModal()">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                    </svg>
                    <span>Sesuaikan & Potong Foto (Crop Image)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Geser, perbesar (zoom), putar, atau pilih rasio potongan foto.</p>
            </div>
            <button type="button" onclick="closeCropperModal()" class="text-slate-400 hover:text-white p-1.5 rounded-xl hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Canvas Container -->
        <div class="w-full h-72 sm:h-96 bg-slate-950 rounded-xl overflow-hidden flex items-center justify-center border border-slate-800 relative">
            <img id="cropper-image" src="" alt="To Crop" class="max-w-full max-h-full block">
        </div>

        <!-- Aspect Ratio Selection Row -->
        <div class="flex items-center gap-1.5 flex-wrap text-xs pt-1 border-t border-slate-800/80">
            <span class="text-slate-400 font-semibold mr-1">Rasio Bingkai:</span>
            <button type="button" onclick="cropperSetRatio(NaN)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white text-xs font-semibold transition-colors">Bebas</button>
            <button type="button" onclick="cropperSetRatio(1)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white text-xs font-semibold transition-colors">1:1 (Avatar/Kotak)</button>
            <button type="button" onclick="cropperSetRatio(3/2)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white text-xs font-semibold transition-colors">3:2 (KTP/STNK/Toll)</button>
            <button type="button" onclick="cropperSetRatio(4/3)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white text-xs font-semibold transition-colors">4:3 (Hardware)</button>
            <button type="button" onclick="cropperSetRatio(16/9)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white text-xs font-semibold transition-colors">16:9 (Armada/Panggung)</button>
        </div>

        <!-- Controls toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="cropperRotate(-90)" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all" title="Putar Kiri 90°">
                    ⟲ Putar
                </button>
                <button type="button" onclick="cropperRotate(90)" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all" title="Putar Kanan 90°">
                    Putar ⟳
                </button>
                <button type="button" onclick="cropperZoom(0.1)" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-all" title="Perbesar (+)">
                    🔍+
                </button>
                <button type="button" onclick="cropperZoom(-0.1)" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-all" title="Perkecil (-)">
                    🔍-
                </button>
                <button type="button" onclick="cropperReset()" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-all" title="Reset Bingkai">
                    Reset
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="closeCropperModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                    Batal
                </button>
                <button type="button" onclick="applyCroppedImage()" class="px-5 py-2 rounded-xl bg-linear-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Terapkan Potongan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCropper = null;
    let activeFileInput = null;
    let activePreviewImg = null;
    let activePlaceholder = null;
    let activeFileName = 'cropped-image.jpg';
    let activeOnSuccess = null;

    window.openCropper = function(options) {
        const file = options.file;
        if (!file) return;

        activeFileInput = options.fileInput;
        activePreviewImg = options.previewImg;
        activePlaceholder = options.placeholder;
        activeFileName = file.name || 'cropped-image.jpg';
        activeOnSuccess = options.onSuccess || null;
        const aspectRatio = options.aspectRatio !== undefined ? options.aspectRatio : 16 / 9;

        const modal = document.getElementById('cropper-modal');
        const imageEl = document.getElementById('cropper-image');

        const reader = new FileReader();
        reader.onload = function(e) {
            imageEl.src = e.target.result;
            modal.style.display = 'flex';
            modal.classList.remove('hidden');

            if (currentCropper) {
                currentCropper.destroy();
            }

            // Wait a tiny bit for DOM image load
            setTimeout(() => {
                currentCropper = new Cropper(imageEl, {
                    aspectRatio: isNaN(aspectRatio) ? NaN : aspectRatio,
                    viewMode: 1,
                    autoCropArea: 0.9,
                    responsive: true,
                    background: false,
                    movable: true,
                    zoomable: true,
                    rotatable: true,
                    scalable: true,
                });
            }, 60);
        };
        reader.readAsDataURL(file);
    };

    window.cropperSetRatio = function(ratio) {
        if (currentCropper) {
            currentCropper.setAspectRatio(ratio);
        }
    };

    window.closeCropperModal = function() {
        const modal = document.getElementById('cropper-modal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
    };

    // Close on Escape key press
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('cropper-modal');
            if (modal && modal.style.display !== 'none') {
                closeCropperModal();
            }
        }
    });

    window.cropperRotate = function(degree) {
        if (currentCropper) currentCropper.rotate(degree);
    };

    window.cropperZoom = function(ratio) {
        if (currentCropper) currentCropper.zoom(ratio);
    };

    window.cropperReset = function() {
        if (currentCropper) currentCropper.reset();
    };

    window.applyCroppedImage = function() {
        if (!currentCropper || !activeFileInput) return;

        currentCropper.getCroppedCanvas({
            maxWidth: 1600,
            maxHeight: 1600,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }).toBlob(function(blob) {
            if (!blob) return;

            // Masukkan hasil potong sebagai file objek nyata ke file input form
            const croppedFile = new File([blob], activeFileName, { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            activeFileInput.files = dataTransfer.files;

            // Update gambar pratinjau
            if (activePreviewImg) {
                activePreviewImg.src = URL.createObjectURL(blob);
                activePreviewImg.classList.remove('hidden');
            }
            if (activePlaceholder) {
                activePlaceholder.classList.add('hidden');
            }

            if (activeOnSuccess && typeof activeOnSuccess === 'function') {
                activeOnSuccess();
            }

            closeCropperModal();
        }, 'image/jpeg', 0.92);
    };

    window.setupImageCropper = function(inputId, previewImgId, placeholderId, defaultRatio, reCropBtnId, fieldPrefix) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewImgId);
        const placeholder = document.getElementById(placeholderId);
        const reCropBtn = reCropBtnId ? document.getElementById(reCropBtnId) : null;
        const prefix = fieldPrefix || inputId.replace('_input', '');
        const clearBtn = document.getElementById(`clear_${prefix}_btn`);
        const removeBtn = document.getElementById(`remove_${prefix}_btn`);
        const undoBtn = document.getElementById(`undo_${prefix}_btn`);
        const removeInput = document.getElementById(`remove_${prefix}_input`);

        if (!input) return;

        let originalFile = null;

        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                originalFile = this.files[0];
                window.openCropper({
                    file: originalFile,
                    fileInput: this,
                    previewImg: preview,
                    placeholder: placeholder,
                    aspectRatio: defaultRatio,
                    onSuccess: function() {
                        if (reCropBtn) reCropBtn.classList.remove('hidden');
                        if (clearBtn) clearBtn.classList.remove('hidden');
                        if (removeBtn) removeBtn.classList.remove('hidden');
                        if (undoBtn) undoBtn.classList.add('hidden');
                        if (removeInput) removeInput.value = '0';
                    }
                });
            }
        });

        if (reCropBtn) {
            reCropBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const fileToCrop = originalFile || (input.files && input.files[0]);
                if (fileToCrop) {
                    window.openCropper({
                        file: fileToCrop,
                        fileInput: input,
                        previewImg: preview,
                        placeholder: placeholder,
                        aspectRatio: defaultRatio,
                        onSuccess: function() {
                            if (reCropBtn) reCropBtn.classList.remove('hidden');
                            if (clearBtn) clearBtn.classList.remove('hidden');
                            if (removeBtn) removeBtn.classList.remove('hidden');
                            if (undoBtn) undoBtn.classList.add('hidden');
                            if (removeInput) removeInput.value = '0';
                        }
                    });
                }
            });
        }
    };

    window.removeImage = function(fieldPrefix) {
        const input = document.getElementById(`${fieldPrefix}_input`);
        const preview = document.getElementById(`${fieldPrefix}_preview`);
        const placeholder = document.getElementById(`${fieldPrefix}_placeholder`);
        const removeInput = document.getElementById(`remove_${fieldPrefix}_input`);
        const removeBtn = document.getElementById(`remove_${fieldPrefix}_btn`);
        const undoBtn = document.getElementById(`undo_${fieldPrefix}_btn`);
        const cropBtn = document.getElementById(`crop_${fieldPrefix}_btn`);
        const clearBtn = document.getElementById(`clear_${fieldPrefix}_btn`);

        if (input) input.value = '';
        if (preview) {
            preview.dataset.prevSrc = preview.src;
            preview.classList.add('hidden');
        }
        if (placeholder) placeholder.classList.remove('hidden');
        if (removeInput) removeInput.value = '1';
        if (removeBtn) removeBtn.classList.add('hidden');
        if (undoBtn) undoBtn.classList.remove('hidden');
        if (cropBtn) cropBtn.classList.add('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
    };

    window.undoRemoveImage = function(fieldPrefix, originalSrc) {
        const preview = document.getElementById(`${fieldPrefix}_preview`);
        const placeholder = document.getElementById(`${fieldPrefix}_placeholder`);
        const removeInput = document.getElementById(`remove_${fieldPrefix}_input`);
        const removeBtn = document.getElementById(`remove_${fieldPrefix}_btn`);
        const undoBtn = document.getElementById(`undo_${fieldPrefix}_btn`);
        const cropBtn = document.getElementById(`crop_${fieldPrefix}_btn`);

        if (removeInput) removeInput.value = '0';
        const srcToRestore = originalSrc || (preview ? preview.dataset.prevSrc : null);
        if (preview && srcToRestore) {
            preview.src = srcToRestore;
            preview.classList.remove('hidden');
        }
        if (placeholder && srcToRestore) {
            placeholder.classList.add('hidden');
        }
        if (removeBtn) removeBtn.classList.remove('hidden');
        if (undoBtn) undoBtn.classList.add('hidden');
        if (cropBtn) cropBtn.classList.remove('hidden');
    };

    window.clearNewImage = function(fieldPrefix) {
        const input = document.getElementById(`${fieldPrefix}_input`);
        const preview = document.getElementById(`${fieldPrefix}_preview`);
        const placeholder = document.getElementById(`${fieldPrefix}_placeholder`);
        const cropBtn = document.getElementById(`crop_${fieldPrefix}_btn`);
        const clearBtn = document.getElementById(`clear_${fieldPrefix}_btn`);

        if (input) input.value = '';
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        if (placeholder) placeholder.classList.remove('hidden');
        if (cropBtn) cropBtn.classList.add('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
    };
</script>
