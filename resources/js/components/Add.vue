<template>
    <div class="add-form">
        <form>
            <h6>Добавить тренировку</h6>
            <br>
            <div class="form-group">
                <label for="date">выберите дату</label>
                <datepicker id="date" v-model="date"></datepicker>
            </div>
            <div class="form-group">
                <label for="startTime">время начала</label><br>
                <vue-timepicker input-width="220px" id="startTime" v-model="startTime"></vue-timepicker>
            </div>
            <div class="form-group">
                <label for="stopTime">время конца</label><br>
                <vue-timepicker input-width="220px" id="stopTime" v-model="stopTime"></vue-timepicker>
            </div>
            <div class="form-group">
                <label for="title">название</label><br>
                <input required type="text" class="form-control" id="title" v-model="title">
            </div>
            <div class="form-group">
                <label for="coach">имя тренера</label><br>
                <input required type="text" class="form-control" id="coach" v-model="coach">
            </div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
    </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue';

export default {
    data() {
        return {
            date: null,
            startTime: null,
            stopTime: null,
            title: '',
            coach: ''
        }
    },
    components: {
        Datepicker,
        VueTimepicker
    },
    methods: {

    }
}
</script>

<style scoped>

</style>
