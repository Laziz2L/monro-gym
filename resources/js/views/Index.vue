<template>
    <div>
        <add @reload="reload" :coach-name="coachName" :client-name='clientName' :user-id="userId"></add>
        <schedule ref="schedule" :coach-user="coachUser" :client-name='clientName' :user-id="userId"></schedule>
    </div>
</template>

<script>
import Add from "../components/Add";
import Schedule from "../components/Schedule";

export default {
    props: ['coachUser', 'coachName', 'clientName', 'userId'],
    components: {
        Schedule,
        Add
    },
    methods: {
        reload() {
            this.$refs.schedule.load();
        }
    }
}
</script>

<style scoped>

</style>
