<template>
    <div class="add-form">
        <form @submit.prevent="store">
            <h6>Добавить тренировку</h6>
            <br>
            <div class="form-group">
                <label for="date">выберите дату</label>
                <datepicker :monday-first="true" id="date" v-model="form.date"></datepicker>
            </div>
            <div class="form-group">
                <label for="startTime">время начала</label><br>
                <vue-timepicker format="HH:mm" input-width="220px" id="startTime" v-model="form.startTime" hide-clear-button close-on-complete></vue-timepicker>
            </div>
            <div class="form-group">
                <label for="stopTime">время конца</label><br>
                <vue-timepicker format="HH:mm" input-width="220px" id="stopTime" v-model="form.stopTime" hide-clear-button close-on-complete></vue-timepicker>
            </div>
            <div class="form-group">
                <label for="title">название</label><br>
                <input required type="text" class="form-control" id="title" v-model="form.title">
            </div>
            <div class="form-group">
                <label for="coach">имя тренера</label><br>
                <input required type="text" class="form-control" id="coach" v-model="form.coach">
            </div>
            <button type="submit" class="btn btn-success">Добавить</button>
        </form>
    </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue';
import moment from "moment";

export default {
    data() {
        return {
            form: {
                date: null,
                startTime: '',
                stopTime: '',
                title: '',
                coach: ''
            }
        }
    },
    components: {
        Datepicker,
        VueTimepicker
    },
    methods: {
        store() {
            let request = this.form;
            request.date = moment(request.date).format('YYYY-MM-DD');
            axios.post('/api/trainings', request, {
                headers: {
                    "Content-type": "application/json"
                }
            }).then(res => {
                console.log(res.data);
            })
        }
    }
}
</script>

<style scoped>

</style>
