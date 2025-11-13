<template>
  <section class="w-100">
    <!-- Email -->
    <div class="fv-row mb-7">
      <label class="form-label fw-bold text-dark fs-6">Email</label>
      <Field
        class="form-control form-control-lg form-control-solid"
        type="text"
        name="email"
        autocomplete="off"
        v-model="formData.email"
        disabled
      />
      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="email" />
        </div>
      </div>
    </div>

    <!-- OTP -->
    <div class="fv-row mb-7">
      <label
        class="form-label fw-bold text-dark fs-6 d-flex align-items-center justify-content-between"
      >
        Kode OTP
      </label>

      <!-- 🧩 Input manual OTP -->
      <input
        type="text"
        class="form-control form-control-lg form-control-solid text-center"
        placeholder="Masukkan 6 digit OTP"
        maxlength="6"
        v-model="formData.otp_email"
        @input="handleOtp(formData.otp_email)"
      />

      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="otp_email" />
        </div>
      </div>

      <!-- Timer / resend -->
      <div v-if="otpInterval === 0" class="text-gray-400 fw-semobold fs-4 text-end mt-4">
        Tidak menerima kode OTP?
        <button type="button" class="btn p-0 link-primary fw-bold" @click="sendOtp">
          Kirim Ulang
        </button>
      </div>

      <div v-else class="text-gray-400 fw-semobold fs-4 text-end mt-4">
        Kode OTP dapat dikirim ulang dalam
        <span class="fw-bold">{{ otpInterval }}</span> detik
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useOtpIntervalStore } from "../Index.vue";

export default defineComponent({
  name: "VerifyEmail",
  props: {
    formData: {
      type: Object,
      required: true,
    },
  },
  emits: ["onComplete", "sendOtp"],
  setup(props, ctx) {
    const { otpInterval } = useOtpIntervalStore();

    const handleOtp = (value: string) => {
      if (value.length === 6) {
        ctx.emit("onComplete", value);
      }
    };

    const sendOtp = () => {
      ctx.emit("sendOtp");
    };

    return {
      formData: props.formData,
      handleOtp,
      sendOtp,
      otpInterval,
    };
  },
});
</script>

<style scoped>
input.form-control.text-center {
  letter-spacing: 6px;
  font-weight: bold;
  text-transform: uppercase;
}
</style>
