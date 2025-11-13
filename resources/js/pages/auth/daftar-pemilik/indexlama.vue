<template>
    <div class="signup-wrapper">
        <div class="signup-container">
            <!-- Progress Sidebar -->
            <div class="progress-sidebar">
                <div class="sidebar-content">
                    <div class="logo-section">
                        <router-link to="/" class="logo-link">
                            <div class="logo-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                            </div>
                            <span class="logo-text">SLaundry</span>
                        </router-link>
                    </div>

                    <div class="steps-wrapper">
                        <h3 class="steps-title">Proses Pendaftaran</h3>
                        
                        <div class="step-item" :class="{ active: currentStepIndex >= 0, completed: currentStepIndex > 0 }">
                            <div class="step-indicator">
                                <span class="step-number">1</span>
                                <svg v-if="currentStepIndex > 0" class="check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div class="step-content">
                                <h4>Informasi Akun</h4>
                                <p>Masukkan nama, email, dan nomor telepon</p>
                            </div>
                        </div>

                        <div class="step-item" :class="{ active: currentStepIndex >= 1, completed: currentStepIndex > 1 }">
                            <div class="step-indicator">
                                <span class="step-number">2</span>
                                <svg v-if="currentStepIndex > 1" class="check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div class="step-content">
                                <h4>Verifikasi Email</h4>
                                <p>Konfirmasi kode OTP dari email</p>
                            </div>
                        </div>

                        <div class="step-item" :class="{ active: currentStepIndex >= 2, completed: currentStepIndex > 2 }">
                            <div class="step-indicator">
                                <span class="step-number">3</span>
                                <svg v-if="currentStepIndex > 2" class="check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div class="step-content">
                                <h4>Verifikasi Telepon</h4>
                                <p>Konfirmasi kode OTP dari SMS</p>
                            </div>
                        </div>

                        <div class="step-item" :class="{ active: currentStepIndex >= 3, completed: currentStepIndex > 3 }">
                            <div class="step-indicator">
                                <span class="step-number">4</span>
                                <svg v-if="currentStepIndex > 3" class="check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div class="step-content">
                                <h4>Buat Password</h4>
                                <p>Amankan akun Anda</p>
                            </div>
                        </div>
                    </div>

                    <div class="help-section">
                        <div class="help-icon">?</div>
                        <div>
                            <p class="help-title">Butuh Bantuan?</p>
                            <p class="help-text">Hubungi tim support kami</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <div class="form-container">
                    <div class="form-header">
                        <h1 class="form-title">Buat Akun Baru</h1>
                        <p class="form-subtitle">Bergabunglah dengan SLaundry dan kelola bisnis Anda dengan mudah</p>
                    </div>

                    <div class="stepper" id="kt_create_account_stepper" ref="horizontalWizardRef">
                        <!-- Hidden stepper nav -->
                        <div class="stepper-nav py-5 mt-5 d-none">
                            <div class="stepper-item current" data-kt-stepper-element="nav">
                                <h3 class="stepper-title">Akun</h3>
                            </div>
                            <div class="stepper-item" data-kt-stepper-element="nav">
                                <h3 class="stepper-title">Verifikasi Email</h3>
                            </div>
                            <div class="stepper-item" data-kt-stepper-element="nav">
                                <h3 class="stepper-title">Verifikasi Telepon</h3>
                            </div>
                            <div class="stepper-item" data-kt-stepper-element="nav">
                                <h3 class="stepper-title">Password</h3>
                            </div>
                        </div>

                        <!-- Form -->
                        <form class="form-content" novalidate id="kt_create_account_form" @submit="handleStep">
                            <!-- Step 1 -->
                            <div class="current" data-kt-stepper-element="content">
                                <Credential :formData="formData"></Credential>
                            </div>

                            <!-- Step 2 -->
                            <div data-kt-stepper-element="content">
                                <VerifyEmail
                                    :formData="formData"
                                    @on-complete="handleOtpEmail"
                                    @send-otp="sendOtpEmail"
                                ></VerifyEmail>
                            </div>

                            <!-- Step 3 -->
                            <div data-kt-stepper-element="content">
                                <VerifyPhone
                                    :formData="formData"
                                    @on-complete="handleOtpPhone"
                                    @send-otp="sendOtpPhone"
                                ></VerifyPhone>
                            </div>

                            <!-- Step 4 -->
                            <div data-kt-stepper-element="content">
                                <Password :formData="formData"></Password>
                            </div>

                            <!-- Actions -->
                            <div class="form-actions">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-kt-stepper-action="previous"
                                    @click="previousStep"
                                    v-if="currentStepIndex > 0"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                                    </svg>
                                    Kembali
                                </button>

                                <button
                                    type="submit"
                                    id="submit-form"
                                    class="btn btn-primary"
                                    data-kt-stepper-action="submit"
                                    v-if="currentStepIndex === totalSteps - 1"
                                >
                                    <span class="indicator-label">
                                        Daftar Sekarang
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                    <span class="indicator-progress">
                                        Memproses...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>

                                <button
                                    v-else
                                    type="submit"
                                    id="next-form"
                                    class="btn btn-primary"
                                >
                                    <span class="indicator-label">
                                        Selanjutnya
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                    <span class="indicator-progress">
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="divider">
                        <span>atau</span>
                    </div>

                    <div class="signin-prompt">
                        <p>Sudah memiliki akun?</p>
                        <router-link to="/login-pemilik" class="signin-link">
                            Masuk ke Akun Anda
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref, onMounted, computed } from "vue";
import * as Yup from "yup";
import create from "vue-zustand";

import { StepperComponent } from "@/assets/ts/components";
import { useForm } from "vee-validate";
// import Credential from "./steps/Credentiall.vue";
import Credential from "./steps/Credintiall.vue";
import VerifyEmail from "./steps/VerifyEmail.vue";
import VerifyPhone from "./steps/VerifyPhone.vue";
import Password from "./steps/Password.vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";
import router from "@/router";
import { useSetting } from "@/services";

interface ICredential {
    nama?: string;
    email?: string;
    phone?: string;
}

interface IVerifyEmail {
    otp_email?: string;
}

interface IVerifyPhone {
    otp_phone?: string;
}

interface IPassword {
    password?: string;
    password_confirmation?: string;
}

interface CreateAccount
    extends ICredential,
        IVerifyEmail,
        IVerifyPhone,
        IPassword {}

interface IOtpInterval {
    otpInterval: number;
    setOtpInterval: (otpInterval: number) => void;
}

export const useOtpIntervalStore = create<IOtpInterval>((set) => ({
    otpInterval: 0,
    setOtpInterval: (otpInterval: number) => set({ otpInterval }),
}));

export default defineComponent({
    name: "login-pemilik",
    components: {
        Credential,
        VerifyEmail,
        VerifyPhone,
        Password,
    },
    setup() {
        const { data: setting = {} } = useSetting();

        const _stepperObj = ref<StepperComponent | null>(null);
        const horizontalWizardRef = ref<HTMLElement | null>(null);
        const currentStepIndex = ref(0);

        const formData = ref<CreateAccount>({
            nama: "",
            email: "",
            phone: "",
            otp_email: "",
            otp_phone: "",
            password: "",
            password_confirmation: "",
        });

        onMounted(() => {
            _stepperObj.value = StepperComponent.createInsance(
                horizontalWizardRef.value as HTMLElement
            );
        });

        const createAccountSchema = [
            Yup.object({
                nama: Yup.string()
                    .required("Nama tidak boleh kosong")
                    .label("Nama"),
                email: Yup.string()
                    .email()
                    .required("Email tidak boleh kosong")
                    .label("Email"),
                phone: Yup.string()
                    .matches(/^08[0-9]\d{8,11}$/, "No. Telepon tidak valid")
                    .required("No. Telepon tidak boleh kosong")
                    .label("No. Telepon"),
            }),
            Yup.object({}),
            Yup.object({}),
            Yup.object({
                password: Yup.string()
                    .min(8, "Password minimal terdiri dari 8 karakter")
                    .required("Password tidak boleh kosong")
                    .label("Password"),
                password_confirmation: Yup.string()
                    .oneOf(
                        [Yup.ref("password")],
                        "Konfirmasi Password tidak sesuai"
                    )
                    .required("Konfirmasi Password tidak boleh kosong")
                    .label("Konfirmasi Password"),
            }),
        ];

        const currentSchema = computed(() => {
            return createAccountSchema[currentStepIndex.value];
        });

        const { resetForm, handleSubmit } = useForm<
            ICredential | IVerifyEmail | IVerifyPhone | IPassword
        >({
            validationSchema: currentSchema,
        });

        const totalSteps = computed(() => {
            if (_stepperObj.value) {
                return _stepperObj.value.totalStepsNumber;
            } else {
                return 1;
            }
        });

        const { otpInterval, setOtpInterval } = useOtpIntervalStore();

        const sendOtpEmail = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/get/email/otp", {
                    email: formData.value.email,
                    nama: formData.value.nama,
                })
                .then((res) => {
                    toast.success("Kode OTP berhasil dikirim ke Email Anda");
                    unblockBtn("#next-form");
                    callback && callback();

                    setOtpInterval.value(30);
                    handleOtpInterval();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const checkOtpEmail = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/check/email/otp", {
                    email: formData.value.email,
                    otp: formData.value.otp_email,
                })
                .then((res) => {
                    toast.success("Email berhasil diverifikasi");
                    unblockBtn("#next-form");
                    callback && callback();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const sendOtpPhone = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/get/phone/otp", {
                    phone: formData.value.phone,
                })
                .then((res) => {
                    toast.success(
                        "Kode OTP berhasil dikirim ke No. Telepon Anda"
                    );
                    unblockBtn("#next-form");
                    callback && callback();

                    setOtpInterval.value(30);
                    handleOtpInterval();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const checkOtpPhone = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/check/phone/otp", {
                    phone: formData.value.phone,
                    otp: formData.value.otp_phone,
                })
                .then((res) => {
                    toast.success("No. Telepon berhasil diverifikasi");
                    unblockBtn("#next-form");
                    callback && callback();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const handleStep = handleSubmit((values) => {
            resetForm({
                values: {
                    ...formData.value,
                },
            });

            if (currentStepIndex.value === 0) {
                sendOtpEmail(() => {
                    formData.value = { ...values };

                    currentStepIndex.value++;

                    if (!_stepperObj.value) {
                        return;
                    }

                    _stepperObj.value.goNext();
                });
            } else if (currentStepIndex.value === 1) {
                checkOtpEmail(() =>
                    sendOtpPhone(() => {
                        formData.value = { ...values };

                        currentStepIndex.value++;

                        if (!_stepperObj.value) {
                            return;
                        }

                        _stepperObj.value.goNext();
                    })
                );
            } else if (currentStepIndex.value === 2) {
                checkOtpPhone(() => {
                    formData.value = { ...values };

                    currentStepIndex.value++;

                    if (!_stepperObj.value) {
                        return;
                    }

                    _stepperObj.value.goNext();
                });
            } else if (currentStepIndex.value === 3) {
                formData.value = { ...values };

                formSubmit(values);
            } else {
                formData.value = { ...values };

                currentStepIndex.value++;

                if (!_stepperObj.value) {
                    return;
                }

                _stepperObj.value.goNext();
            }
        });

        const previousStep = () => {
            if (!_stepperObj.value) {
                return;
            }

            currentStepIndex.value--;

            _stepperObj.value.goPrev();
        };

        const formSubmit = (values: CreateAccount) => {
            blockBtn("#submit-form");

            axios
                .post("/auth/register", values)
                .then((res) => {
                    toast.success("Akun berhasil dibuat");
                    router.push({ name: "login-pemilik" });
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#submit-form");
                });
        };

        const timeIntv = ref<any>(null);
        const handleOtpInterval = () => {
            clearInterval(timeIntv.value);

            timeIntv.value = setInterval(() => {
                setOtpInterval.value(otpInterval.value - 1);

                if (otpInterval.value === 0) {
                    clearInterval(timeIntv.value);
                }
            }, 1000);
        };

        return {
            horizontalWizardRef,
            previousStep,
            handleStep,
            formSubmit,
            totalSteps,
            currentStepIndex,
            getAssetPath,
            formData,
            sendOtpEmail,
            sendOtpPhone,
            resetForm,
            setting,
        };
    },
    methods: {
        handleOtpEmail(value: string) {
            this.resetForm({
                values: {
                    ...this.formData,
                    otp_email: value,
                },
            });
            this.formData.otp_email = value;
        },
        handleOtpPhone(value: string) {
            this.resetForm({
                values: {
                    ...this.formData,
                    otp_phone: value,
                },
            });
            this.formData.otp_phone = value;
        },
    },
});
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.signup-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow: auto;
}

.signup-container {
    display: flex;
    min-height: 100%;
    max-width: 1400px;
    margin: 0 auto;
}

/* Progress Sidebar */
.progress-sidebar {
    width: 380px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 40px 30px;
    color: white;
    display: flex;
    flex-direction: column;
}

.sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.logo-section {
    margin-bottom: 50px;
}

.logo-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: white;
}

.logo-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
}

.logo-icon svg {
    width: 28px;
    height: 28px;
}

.logo-text {
    font-size: 28px;
    font-weight: 700;
}

.steps-wrapper {
    flex: 1;
}

.steps-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 30px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.step-item {
    display: flex;
    gap: 20px;
    margin-bottom: 35px;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step-item.active {
    opacity: 1;
}

.step-item.completed {
    opacity: 0.8;
}

.step-indicator {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    transition: all 0.3s ease;
}

.step-item.active .step-indicator {
    background: white;
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.step-item.completed .step-indicator {
    background: #4ade80;
}

.step-number {
    font-size: 18px;
    font-weight: 700;
    color: white;
}

.step-item.active .step-number {
    color: #667eea;
}

.check-icon {
    width: 24px;
    height: 24px;
    position: absolute;
}

.step-content h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
}

.step-content p {
    font-size: 13px;
    opacity: 0.8;
    line-height: 1.4;
}

.help-section {
    display: flex;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    margin-top: auto;
}

.help-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.help-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 3px;
}

.help-text {
    font-size: 12px;
    opacity: 0.8;
}

/* Form Section */
.form-section {
    flex: 1;
    background: white;
    padding: 60px;
    overflow-y: auto;
}

.form-container {
    max-width: 600px;
    margin: 0 auto;
}

.form-header {
    margin-bottom: 40px;
}

.form-title {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.form-subtitle {
    font-size: 16px;
    color: #6b7280;
    line-height: 1.5;
}

.stepper {
    margin-bottom: 40px;
}

.form-content {
    width: 100%;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #f3f4f6;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 140px;
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-left: auto;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #f3f4f6;
    color: #6b7280;
}

.btn-secondary:hover {
    background: #e5e7eb;
    color: #374151;
}

.indicator-progress {
    display: none;
}

.divider {
    position: relative;
    text-align: center;
    margin: 40px 0;
}

.divider::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    height: 1px;
    background: #e5e7eb;
}

.divider span {
    position: relative;
    background: white;
    padding: 0 15px;
    color: #9ca3af;
    font-size: 14px;
}

.signin-prompt {
    text-align: center;
}

.signin-prompt p {
    color: #6b7280;
    margin-bottom: 12px;
    font-size: 15px;
}

.signin-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    padding: 12px 24px;
    border: 2px solid #667eea;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.signin-link:hover {
    background: #667eea;
    color: white;
}

/* Responsive */
@media (max-width: 1200px) {
    .progress-sidebar {
        width: 320px;
        padding: 30px 25px;
    }

    .form-section {
        padding: 40px;
    }
}

@media (max-width: 968px) {
    .signup-container {
        flex-direction: column;
    }

    .progress-sidebar {
        width: 100%;
        min-height: auto;
        padding: 30px 20px;
    }

    .steps-wrapper {
        display: none;
    }

    .help-section {
        display: none;
    }

    .form-section {
        padding: 30px 20px;
    }
}

@media (max-width: 640px) {
    .form-section {
        padding: 25px 15px;
    }

    .form-title {
        font-size: 26px;
    }

    .form-subtitle {
        font-size: 14px;
    }

    .btn {
        padding: 12px 20px;
        font-size: 14px;
        min-width: 120px;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-primary {
        margin-left: 0;
    }
}
</style>