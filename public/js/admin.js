document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-icon-picker]').forEach((picker) => {
        const trigger = picker.querySelector('[data-icon-trigger]');
        const gallery = picker.querySelector('[data-icon-gallery]');
        const input = picker.querySelector('[data-icon-input]');
        const preview = picker.querySelector('[data-icon-preview]');

        if (!trigger || !gallery || !input || !preview) {
            return;
        }

        trigger.addEventListener('click', () => {
            const isHidden = gallery.hasAttribute('hidden');
            document.querySelectorAll('[data-icon-gallery]').forEach((el) => el.setAttribute('hidden', ''));
            if (isHidden) {
                gallery.removeAttribute('hidden');
            }
        });

        gallery.querySelectorAll('[data-icon-value]').forEach((option) => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-icon-value');
                input.value = value;
                gallery.querySelectorAll('.icon-option').forEach((el) => el.classList.remove('active'));
                option.classList.add('active');

                if (value === 'badge-2') {
                    preview.innerHTML = '<span class="cotacao-badge-2">2</span>';
                } else {
                    preview.innerHTML = `<i class="fa-solid ${value}"></i>`;
                }

                gallery.setAttribute('hidden', '');
            });
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-icon-picker]')) {
            document.querySelectorAll('[data-icon-gallery]').forEach((el) => el.setAttribute('hidden', ''));
        }
    });

    const rowsContainer = document.getElementById('modalidades-rows');
    const addButton = document.getElementById('add-modalidade-row');
    const template = document.getElementById('modalidade-row-template');

    if (rowsContainer && addButton && template) {
        addButton.addEventListener('click', () => {
            const node = template.content.cloneNode(true);
            rowsContainer.appendChild(node);
        });

        rowsContainer.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-row]');
            if (!button) {
                return;
            }

            const row = button.closest('.modalidade-row');
            if (!row) {
                return;
            }

            if (rowsContainer.querySelectorAll('.modalidade-row').length === 1) {
                row.querySelector('input').value = '';
                return;
            }

            row.remove();
        });
    }

    const formatMoneyBRL = (rawDigits) => {
        const digits = String(rawDigits || '').replace(/\D/g, '');

        if (!digits) {
            return '';
        }

        const amount = (Number(digits) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });

        return amount;
    };

    document.querySelectorAll('[data-mask-money]').forEach((input) => {
        if (input.value) {
            const digits = input.value.replace(/\D/g, '');
            input.value = digits ? formatMoneyBRL(digits) : '';
        }

        input.addEventListener('input', () => {
            input.value = formatMoneyBRL(input.value);
        });

        input.addEventListener('blur', () => {
            const digits = input.value.replace(/\D/g, '');
            input.value = digits ? formatMoneyBRL(digits) : '';
        });
    });

    initImageCropper();
});

function initImageCropper() {
    const input = document.querySelector('[data-image-cropper]');
    const modalEl = document.getElementById('imageCropperModal');
    const sourceImg = document.getElementById('imageCropperSource');
    const confirmBtn = document.getElementById('imageCropperConfirm');
    const previewWrap = document.querySelector('[data-crop-preview]');
    const previewImg = previewWrap ? previewWrap.querySelector('img') : null;
    const form = document.querySelector('[data-resultado-form]');
    const currentImage = document.querySelector('[data-current-image]');

    if (!input || !modalEl || !sourceImg || !confirmBtn || typeof Cropper === 'undefined' || typeof bootstrap === 'undefined') {
        return;
    }

    const OUTPUT_WIDTH = 1200;
    const OUTPUT_HEIGHT = 750;
    const ASPECT_RATIO = 16 / 10;

    let cropper = null;
    let objectUrl = null;
    let croppedReady = false;
    const cropRequired = input.getAttribute('data-crop-required') === 'true';
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    const destroyCropper = () => {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
        sourceImg.removeAttribute('src');
    };

    const clearSelection = () => {
        input.value = '';
        croppedReady = false;
        if (previewWrap) {
            previewWrap.hidden = true;
        }
        if (previewImg) {
            previewImg.removeAttribute('src');
        }
    };

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) {
            clearSelection();
            return;
        }

        if (!file.type.startsWith('image/')) {
            clearSelection();
            alert('Selecione um arquivo de imagem válido.');
            return;
        }

        croppedReady = false;
        destroyCropper();
        objectUrl = URL.createObjectURL(file);

        const openCropper = () => {
            modal.show();
        };

        if (sourceImg.complete && sourceImg.getAttribute('src') === objectUrl) {
            openCropper();
            return;
        }

        sourceImg.onload = openCropper;
        sourceImg.onerror = () => {
            clearSelection();
            destroyCropper();
            alert('Não foi possível carregar a imagem selecionada.');
        };
        sourceImg.src = objectUrl;
    });

    modalEl.addEventListener('shown.bs.modal', () => {
        if (!sourceImg.getAttribute('src') || cropper) {
            return;
        }

        cropper = new Cropper(sourceImg, {
            aspectRatio: ASPECT_RATIO,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            background: false,
            movable: true,
            zoomable: true,
            scalable: false,
            rotatable: false,
        });
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        destroyCropper();
        if (!croppedReady) {
            clearSelection();
        }
    });

    confirmBtn.addEventListener('click', () => {
        if (!cropper) {
            return;
        }

        const canvas = cropper.getCroppedCanvas({
            width: OUTPUT_WIDTH,
            height: OUTPUT_HEIGHT,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            alert('Não foi possível gerar o recorte.');
            return;
        }

        canvas.toBlob((blob) => {
            if (!blob) {
                alert('Não foi possível gerar a imagem recortada.');
                return;
            }

            const fileName = `resultado-${Date.now()}.jpg`;
            const croppedFile = new File([blob], fileName, { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            input.files = dataTransfer.files;
            croppedReady = true;

            if (previewImg && previewWrap) {
                previewImg.src = URL.createObjectURL(blob);
                previewWrap.hidden = false;
            }

            if (currentImage) {
                currentImage.hidden = true;
            }

            modal.hide();
        }, 'image/jpeg', 0.92);
    });

    if (form && cropRequired) {
        form.addEventListener('submit', (event) => {
            if (!input.files || !input.files.length || !croppedReady) {
                event.preventDefault();
                alert('Selecione uma imagem e aplique o recorte antes de salvar.');
            }
        });
    }
}