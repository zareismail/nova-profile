<template>
  <loading-view :loading="loading">
    <profile-card :resource="resource" :resource-id="resourceId" :resource-name="resourceName"> 
      <div class="w-1/5"> 
        <img :src="card.avatar" class="rounded-full avatar">
        
        <div class="w-full px-4 mt-4">
          <router-link
            class="dim no-underline text-info mr-4"
            :to="{
              name: 'edit',
              params: {
                resourceId: resourceId,
                resourceName: resourceName,
              } 
            }"
          >{{ __('Edit') }}</router-link>
          <router-link
            class="dim no-underline text-info"
            :to="{
              name: 'detail',
              params: {
                resourceId: resourceId,
                resourceName: resourceName,
              } 
            }"
          >{{ __('View') }}</router-link>
        </div>
      </div>
      <div class="w-4/5 flex flex-wrap">
        <div 
          class="flex flex-wrap px-1 py-4 w-1/2 justify-between" 
          v-for="field in fields"
          :class="{'w-full': field.fullwidth}"
          :class="field.width"
        >
          <h4 v-if="! field.withoutLabel">{{ field.name }}:</h4>
          <component 
            class="px-8 text-80"
            :key="field.name"
            :is="`index-` + field.component"
            :field="field"
            :resource="resource"
            :resource-id="resourceId"
            :resource-name="resourceName"
          />
        </div>  
      </div>
    </profile-card>
  </loading-view>
</template>

<script>
import { Minimum } from 'laravel-nova'
import ProfileCard from './ProfileCard.vue'

export default {
  components: {
    ProfileCard: ProfileCard
  },

  props: [
      'card',

      // The following props are only available on resource detail cards...
      // 'resource',
      // 'resourceId',
      // 'resourceName',
  ],

  data: () => ({
    resource: null,
    loading: true,
  }),

  async mounted() {
    await this.getResource() 
  },

  methods: { 
    /**
     * Get the resource information.
     */
    async getResource() {
      this.resource = null
      this.loading = true

      return Minimum(
        Nova.request().get(
          '/nova-api/' + this.resourceName + '/' + this.resourceId, 
          {
            params: {
              card: 'profile'
            }
          }
        )
      )
        .then(({ data: { resource } }) => {
          this.resource = resource
          this.loading = false
        })
        .catch(error => {
          if (error.response.status >= 500) {
            Nova.$emit('error', error.response.data.message)
            return
          }

          if (error.response.status === 404 && this.initialLoading) {
            this.$router.push({ name: '404' })
            return
          }

          if (error.response.status === 403) {
            this.$router.push({ name: '403' })
            return
          }

          Nova.error(this.__('This resource no longer exists'))

          this.$router.push({
            name: 'dashborad.custom', 
          })
        })
    },

    isAvatar(field) {
      return field.name === this.card.avatar;
    }
  },

  computed: {
    resourceId() {
      return this.card.resourceId
    },

    resourceName() {
      return this.card.resourceName
    },

    resourceFields() {
      return this.resource ? this.resource.fields : []; 
    },

    fields() { 
      return this.resourceFields.filter(field => ! this.isAvatar(field));
    },

    avatarField() {
      return this.resourceFields.find(field => this.isAvatar(field)); 
    }
  }
}
</script>

<style scoped>
.avatar {
  width: 125px;
  height: 125px;
}
</style>