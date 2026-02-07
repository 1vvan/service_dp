<template>
    <el-dialog
        v-model="dialogVisible"
        title="Додати фото автомобіля"
        width="480px"
        :close-on-click-modal="false"
        @close="handleClose"
        class="upload-photo-modal"
    >
        <template #header>
            <div class="upload-photo-header">
                <h2 class="upload-photo-title">
                    <el-icon><Picture /></el-icon>
                    Завантажити фото
                </h2>
                <p class="upload-photo-description">
                    Виберіть одне або кілька зображень (JPG, PNG, GIF, WebP до 5 МБ кожне)
                </p>
            </div>
        </template>

        <el-upload
            ref="uploadRef"
            class="car-photo-upload"
            drag
            :auto-upload="false"
            :limit="20"
            :show-file-list="false"
            multiple
            :on-change="handleFileChange"
            :on-exceed="handleExceed"
            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
        >
            <el-icon class="el-icon--upload"><UploadFilled /></el-icon>
            <div class="el-upload__text">
                Перетягніть файли сюди або <em>натисніть для вибору</em>
            </div>
            <template #tip>
                <div class="el-upload__tip">
                    Формати: JPG, PNG, GIF, WebP. Максимум 5 МБ на файл. До 20 фото за раз.
                </div>
            </template>
        </el-upload>

        <div v-if="selectedFiles.length" class="preview-section">
            <div class="preview-grid">
                <div v-for="(item, index) in selectedFiles" :key="index" class="preview-item">
                    <img :src="item.previewUrl" alt="Preview" class="preview-image" />
                    <p class="preview-filename">{{ item.file.name }}</p>
                    <el-button
                        type="danger"
                        size="small"
                        circle
                        class="preview-remove-btn"
                        @click="removeFile(index)"
                    >
                        <el-icon><Close /></el-icon>
                    </el-button>
                </div>
            </div>
        </div>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="handleClose" class="cancel-button">Скасувати</el-button>
                <el-button type="primary" :loading="uploading" :disabled="!selectedFiles.length" @click="handleUpload">
                    Завантажити ({{ selectedFiles.length }})
                </el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script>
import { Picture, UploadFilled, Close } from '@element-plus/icons-vue';
import axios from 'axios';

export default {
    name: 'UploadCarPhotoModal',
    components: {
        Picture,
        UploadFilled,
        Close
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false
        },
        carId: {
            type: [Number, String],
            default: null
        }
    },
    emits: ['close', 'uploaded'],
    data() {
        return {
            selectedFiles: [],
            uploading: false
        };
    },
    computed: {
        dialogVisible: {
            get() {
                return this.isOpen;
            },
            set(value) {
                if (!value) {
                    this.$emit('close');
                }
            }
        }
    },
    watch: {
        isOpen(newVal) {
            if (!newVal) {
                this.resetForm();
            }
        }
    },
    methods: {
        handleFileChange(file, fileList) {
            const files = fileList.map(f => f.raw).filter(Boolean);
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];

            const validFiles = [];
            for (const f of files) {
                if (!validTypes.includes(f.type)) {
                    this.$message.error(`Файл "${f.name}" — дозволені лише зображення (JPG, PNG, GIF, WebP)`);
                    continue;
                }
                if (f.size / 1024 / 1024 > 5) {
                    this.$message.error(`Файл "${f.name}" — розмір не повинен перевищувати 5 МБ`);
                    continue;
                }
                validFiles.push({
                    file: f,
                    previewUrl: URL.createObjectURL(f)
                });
            }

            this.selectedFiles = validFiles;
        },
        removeFile(index) {
            if (this.selectedFiles[index]?.previewUrl) {
                URL.revokeObjectURL(this.selectedFiles[index].previewUrl);
            }
            this.selectedFiles.splice(index, 1);
        },
        handleExceed() {
            this.$message.warning('Максимум 20 фото за раз');
        },
        async handleUpload() {
            if (!this.selectedFiles.length || !this.carId) return;

            this.uploading = true;
            const formData = new FormData();
            this.selectedFiles.forEach((item, i) => {
                formData.append('photos[]', item.file);
            });

            try {
                const token = this.$store.state.token;
                const config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        ...(token ? { Authorization: `Bearer ${token}` } : {})
                    }
                };
                const response = await axios.post(`/api/cars/${this.carId}/photos`, formData, config);
                this.$message.success(response.data.message || 'Фото успішно завантажено');
                this.$emit('uploaded', response.data.photos || response.data.photo);
                this.handleClose();
            } catch (error) {
                this.$message.error(error.response?.data?.message || 'Помилка завантаження фото');
            } finally {
                this.uploading = false;
            }
        },
        handleClose() {
            this.resetForm();
            this.$emit('close');
        },
        resetForm() {
            this.selectedFiles.forEach(item => {
                item.previewUrl && URL.revokeObjectURL(item.previewUrl);
            });
            this.selectedFiles = [];
            this.$refs.uploadRef?.clearFiles();
        }
    }
};
</script>