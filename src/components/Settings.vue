<template>
  <div class="settings" v-if="!loading">
    <h2>Settings</h2>
    <form @submit.prevent="submitSettings">
      <div class="form-group">
        <label for="numberOfRows">Number of Rows (1-5):</label>
        <input
            id="numberOfRows"
            type="number"
            v-model.number="localSettings.numberOfRows"
            min="1"
            max="5"
        />
        <div v-if="errors.numberOfRows" class="error">{{ errors.numberOfRows }}</div>
      </div>

      <div class="form-group">
        <label>Date Format:</label>
        <div>
          <label>
            <input
                type="radio"
                value="true"
                v-model="localSettings.humanReadableDate"
            />
            Human Readable
          </label>
          <label>
            <input
                type="radio"
                value="false"
                v-model="localSettings.humanReadableDate"
            />
            Unix Timestamp
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>Emails:</label>
        <div v-for="(email, index) in localSettings.emails" :key="index" class="email-field">
          <input
              type="email"
              v-model="localSettings.emails[index]"
          />
          <button
              type="button"
              @click="removeEmail(index)"
              v-if="localSettings.emails.length > 1"
          >
            Remove
          </button>
        </div>
        <button
            type="button"
            class="mmvuejs-btn mmvuejs-btn-md mmvuejs-btn-gray"
            @click="addEmail"
            v-if="localSettings.emails.length < 5"
        >
          Add Email
        </button>
      </div>

      <!-- Submit Button -->
      <button type="submit">Save Settings</button>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex';

export default {
  name: 'Settings',
  data() {
    return {
      localSettings: {
        numberOfRows: 5,
        humanReadableDate: false,
        emails: [],
      },
      errors: {},
      loading: true,
    };
  },
  computed: {
    ...mapState(['settings']),
  },
  methods: {
    ...mapActions(['fetchSettings', 'updateSetting']),
    submitSettings() {
      this.errors = {};

      // Validation for Number of Rows
      if (
          this.localSettings.numberOfRows < 1 ||
          this.localSettings.numberOfRows > 5
      ) {
        this.errors.numberOfRows = 'Please enter a value between 1 and 5.';
      }

      // Validation for Emails
      if (
          this.localSettings.emails.length < 1 ||
          this.localSettings.emails.length > 5
      ) {
        this.errors.emails = 'You must enter between 1 and 5 emails.';
      }

      if (Object.keys(this.errors).length > 0) {
        // Do not proceed if there are validation errors
        return;
      }

      // Prepare settings to update
      const settingsToUpdate = {
        numberOfRows: this.localSettings.numberOfRows,
        humanReadableDate: this.localSettings.humanReadableDate === 'true',
        emails: this.localSettings.emails,
      };

      // Update each setting individually
      const updatePromises = Object.keys(settingsToUpdate).map((key) => {
        return this.updateSetting({key, value: settingsToUpdate[key]});
      });

      Promise.all(updatePromises).then(() => {
        window.showAdminNotice('Settings were successfully saved.');
      }).catch((error) => {
        window.showAdminNotice('Failed to save settings.', 'error');
        console.error(error);
      });
    },
    addEmail() {
      this.localSettings.emails.push('');
    },
    removeEmail(index) {
      this.localSettings.emails.splice(index, 1);
    },
  },
  created() {
    // Fetch settings when component is created
    this.fetchSettings().then(() => {
      this.localSettings = {
        numberOfRows: this.settings.numberOfRows,
        humanReadableDate: this.settings.humanReadableDate ? 'true' : 'false',
        emails: [...this.settings.emails],
      };
      this.loading = false;
    });
  },
};
</script>

<style scoped>
.form-group {
  margin-bottom: 20px;
}

.email-field {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.email-field input {
  flex: 1;
}

.email-field button {
  margin-left: 10px;
}

.error {
  color: red;
  font-size: 0.9em;
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

button[type='submit'] {
  background-color: #e27730;
  border-color: #e27730;
  color: #fff;
  font-size: 14px;
  font-weight: 500;
  padding: 9px 15px;
  min-height: 36px;
  line-height: 16px;
  border-width: 1px;
  border-style: solid;
  border-radius: 3px;
  cursor: pointer;
  display: inline-block;
  margin: 0;
  text-decoration: none;
  text-align: center;
  vertical-align: middle;
  white-space: nowrap;
  text-shadow: none;
  box-shadow: none;
  outline: none;
}
</style>