<template>
  <div class="settings">
    <div class="admin-notices"></div>
    <h2 class="mmvuejs-separate-sections my-0">{{ __('Settings', 'mmvuejs') }}</h2>
    <form @submit.prevent="submitSettings">
      <div class="form-group mmvuejs-separate-sections">
        <label for="numberOfRows" class="mmvuejs-label">{{ __('Number of Rows (1-5)', 'mmvuejs') }}</label>
        <div class="mmvuejs-field">
          <input
              id="numberOfRows"
              type="number"
              v-model.number="settings.numberOfRows"
              min="1"
              max="5"
          />
        </div>
      </div>

      <div class="form-group mmvuejs-separate-sections">
        <label class="mmvuejs-label">{{ __('Date Format', 'mmvuejs') }}</label>
        <div class="mmvuejs-field">
          <span class="mr-10">
            <label for="human-readable-true">
              <input
                  type="radio"
                  :value="true"
                  v-model="settings.humanReadableDate"
                  id="human-readable-true"
              />
              {{ __('Human Readable', 'mmvuejs') }}
            </label>
          </span>
          <span class="mr-10">
            <label for="human-readable-false">
              <input
                  type="radio"
                  :value="false"
                  v-model="settings.humanReadableDate"
                  id="human-readable-false"
              />
              {{ __('Unix Timestamp', 'mmvuejs') }}
            </label>
          </span>
        </div>
      </div>

      <div class="form-group mmvuejs-separate-sections">
        <label class="mmvuejs-label">{{ __('Emails', 'mmvuejs') }}</label>
        <div class="mmvuejs-field">
          <div v-for="(email, index) in settings.emails" :key="index" class="email-field">
            <input
                type="email"
                v-model="settings.emails[index]"
                required
            />
            <button
                type="button"
                @click="removeEmail(index)"
                v-if="settings.emails.length > 1"
            >
              <i class="dashicons dashicons-trash" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button
            type="button"
            class="mmvuejs-btn mmvuejs-btn-md mmvuejs-btn-gray"
            @click="addEmail"
            v-if="settings.emails.length < 5"
        >
          {{ __('Add Email', 'mmvuejs') }}
        </button>
      </div>

      <div class="py-25">
        <button type="submit" class="mmvuejs-btn-orange">{{ __('Save Settings', 'mmvuejs') }}</button>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex';

const {__} = wp.i18n;

export default {
  name: 'Settings',
  computed: {
    ...mapState(['settings']),
  },
  methods: {
    __,
    ...mapActions(['updateSetting']),
    submitSettings() {

      // Prepare settings to update
      const settingsToUpdate = {
        numberOfRows: this.settings.numberOfRows,
        humanReadableDate: this.settings.humanReadableDate,
        emails: this.settings.emails,
      };

      // Update each setting individually
      const updatePromises = Object.keys(settingsToUpdate).map((key) => {
        return this.updateSetting({key, value: settingsToUpdate[key]});
      });

      Promise.all(updatePromises).then(() => {
        window.showAdminNotice(__('Settings were successfully saved.', 'mmvuejs'));
      }).catch((error) => {
        window.showAdminNotice(__('Failed to save settings.', 'mmvuejs'), 'error');
        console.error(error);
      });
    },
    addEmail() {
      this.settings.emails.push('');
    },
    removeEmail(index) {
      this.settings.emails.splice(index, 1);
    },
  },
  created() {
    // Fetch settings when component is created
    if (!this.settings.emails.length) {
      this.$store.dispatch('fetchSettings');
    }
  },
};
</script>

<style scoped>
.email-field button {
  background: none;
  border: 0;
  cursor: pointer;
  margin-left: 0 !important;
}

.dashicons {
  border: 0;
  color: #999;
}

.dashicons:hover {
  border: none;
  color: #d63638;
}

.email-field {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.email-field input {
  flex: 1;
}

.email-field input:focus {
  border: 1px solid #016aab;
  box-shadow: 0 0 0 1px #016aab;
  outline: none;
}

.email-field button {
  margin-left: 10px;
}

input[type='email'] {
  background-color: #fff;
  border: 1px solid #8d8f95;
  border-radius: 4px;
  box-shadow: none;
  color: #2c3337;
  display: inline-block;
  vertical-align: middle;
  padding: 7px 12px;
  margin: 0 10px 0 0;
  max-width: 400px;
  min-height: 36px;
  line-height: 1.3;
}


</style>