<template>
  <div class="card flex flex-wrap p-8 items-center">
    <slot />
  </div> 
</template>

<script> 
export default {
  props: [
      'resource',
      'resourceId',
      'resourceName' 
  ], 
};
</script> 