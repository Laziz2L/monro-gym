<template>
  <div class="schedule">
    <div class="filter">
      <div class="week">
        <button class="week-btn" @click="prevWeek">
          <i class="arrow left"></i>
        </button>
        <p class="week-text">
          <span>{{ filter.startDate }}</span> -
          <span>{{ filter.stopDate }}</span>
        </p>
        <button class="week-btn" @click="nextWeek">
          <i class="arrow right"></i>
        </button>
      </div>
      <div class="dropdown">
        <button
          class="filter-dropdown dropdown-toggle"
          type="button"
          id="dropdownMenuButton1"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          {{ filter.coach != "all" ? filter.coach : "Все тренеры" }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
          <li v-if="filter.coach != 'all'">
            <a class="dropdown-item" href="#" @click="filter.coach = 'all'"
              >Все</a
            >
          </li>
          <li v-for="coach in coaches" :key="coach">
            <a
              v-if="filter.coach != coach"
              class="dropdown-item"
              href="#"
              @click="filter.coach = coach"
              >{{ coach }}</a
            >
          </li>
        </ul>
      </div>
    </div>

    <table class="schedule-table" v-if="!displayMobile">
      <tr>
        <th class="table-clock">
          <img height="20" width="20" src="img/clock.svg" />
        </th>
        <th v-for="(e, i) in 7" class="day-header" :key="e">
          <p>{{ weekDays[i] }}</p>
          <p>{{ weekDaysString[i] }}</p>
        </th>
      </tr>
      <tr v-for="time in table" :key="time.hour">
        <td class="table-time">{{ time.hour }}</td>
        <td v-for="day in weekDaysTable" :key="day">
          <div
            v-for="training in time[day]"
            class="training-card"
            v-bind:class="cardColor(training)"
            :key="training.id"
          >
            <div class="training-status">
              {{ training.available ? "свободно" : training.client_name }}
            </div>
            <div class="training-time">
              {{ training.time_start }} - {{ training.time_stop }}
            </div>
            <div class="training-title">
              {{ training.title }}
            </div>
            <div class="training-coach">
              {{ training.coach_name }}
            </div>
            <button
              v-if="training.available && coachUser == 'false'"
              class="training-btn"
              @click="show(training.id, 1)"
            >
              Записаться
            </button>
            <button
              v-if="training.creator == userId"
              class="training-btn delete"
              @click="show(training.id, 2)"
            >
              Удалить
            </button>
            <button
              v-if="training.client_id == userId"
              class="training-btn delete"
              @click="show(training.id, 3)"
            >
              Отменить
            </button>
          </div>
        </td>
      </tr>
    </table>

    <div class="mini-table" v-if="displayMobile">
      <table class="schedule-table">
        <tr>
          <th class="table-clock">
            <img height="15" width="15" src="img/clock.svg" />
          </th>
          <th
            v-for="(e, i) in 7"
            class="day-header"
            :key="e"
            @click="scrollToElement(weekDaysTable[i])"
          >
            <p>{{ weekDays[i] }}</p>
            <p>{{ shortWeekDaysString[i] }}</p>
          </th>
        </tr>
        <tr v-for="time in table" :key="time.hour">
          <td class="table-time">{{ time.hour }}</td>
          <td v-for="day in weekDaysTable" :key="day">
            <training-dot
              v-for="training in time[day]"
              :key="training.id"
              :training="training"
              :available="training.available && coachUser == 'false'"
              :deletable="training.creator == userId || training.client_id == userId"
            ></training-dot>
          </td>
        </tr>
      </table>
    </div>

    <div class="phone-table" v-if="displayMobile">
      <table
        class="schedule-table"
        v-for="(day, i) in phoneTable"
        :key="i"
        :id="weekDaysTable[i]"
      >
        <tr>
          <th class="table-clock strong-width-30">
            <img height="20" width="20" src="img/clock.svg" />
          </th>
          <th class="day-header strong-width-190">
            <p>{{ weekDays[i] }}</p>
            <p>{{ weekDaysString[i] }}</p>
          </th>
        </tr>
        <tr v-for="[hour, trainings] of Object.entries(day)" :key="hour">
          <td class="table-time strong-width-30">{{ hour }}</td>
          <td class="strong-width-190">
            <div
              v-for="training in trainings"
              class="training-card"
              v-bind:class="cardColor(training)"
              :key="training.id"
            >
              <div class="training-status">
                {{ training.available ? "свободно" : training.client_name }}
              </div>
              <div class="training-time">
                {{ training.time_start }} - {{ training.time_stop }}
              </div>
              <div class="training-title">
                {{ training.title }}
              </div>
              <div class="training-coach">
                {{ training.coach_name }}
              </div>
              <button
                v-if="training.available && coachUser == 'false'"
                class="training-btn"
                @click="show(training.id, 1)"
              >
                Записаться
              </button>
              <button
                v-if="training.creator == userId"
                class="training-btn delete"
                @click="show(training.id, 2)"
              >
                Удалить
              </button>
              <button
                v-if="training.client_id == userId"
                class="training-btn delete"
                @click="show(training.id, 3)"
              >
                Отменить
              </button>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div v-if="showModal">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog" role="document">
              <div v-if="!forDelete && !forUnBook" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Запись на тренировку</h5>
                  <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                    @click="hide"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form @submit.prevent="book">
                    <div class="form-group">
                      <label for="clientName">Ваше имя:</label>
                      <input
                        type="text"
                        class="form-control"
                        id="clientName"
                        v-model="clientName"
                        readonly
                      />
                    </div>

                    <div class="form-group">
                      <input
                        type="checkbox"
                        id="clientNameCheck"
                        v-model="showSecondName"
                      />
                      <label for="clientNameCheck"> Нас будет двое</label>
                    </div>

                    <div v-if="showSecondName" class="form-group">
                      <label for="clientName2">Второе имя:</label>
                      <input
                        type="text"
                        class="form-control"
                        id="clientName2"
                        v-model="secondName"
                        required
                      />
                    </div>
                    <br />
                    <button type="submit" class="btn btn-success">
                      Записаться
                    </button>
                  </form>
                </div>
              </div>
              <div v-if="forDelete" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Удаление тренировки</h5>
                  <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                    @click="hide"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form @submit.prevent="">
                    Вы уверены, что хотите удалить тренировку?
                    <br />
                    <br />
                    <button type="submit" class="btn btn-danger" @click="pop">
                      Удалить
                    </button>
                  </form>
                </div>
              </div>
              <div v-if="forUnBook" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Отмена записи</h5>
                  <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                    @click="hide"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form @submit.prevent="">
                    Вы уверены, что хотите отменить запись?
                    <br />
                    <br />
                    <button type="submit" class="btn btn-danger" @click="unBook">
                      Отменить запись
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import TrainingDot from "../components/TrainingDot";

export default {
  components: {
    TrainingDot,
  },
  props: ["coachUser", "clientName", "userId"],
  data() {
    return {
      currentMonday: null,

      weekDays: [],
      weekDaysString: [
        "Понедельник",
        "Вторник",
        "Среда",
        "Четверг",
        "Пятница",
        "Суббота",
        "Воскресенье",
      ],
      shortWeekDaysString: ["пн", "вт", "ср", "чт", "пт", "сб", "вс"],
      weekDaysTable: ["mon", "tue", "wed", "thu", "fri", "sat", "sun"],

      filter: {
        startDate: null,
        stopDate: null,
        coach: "all",
      },

      coaches: ["Татьяна", "Азалия"],

      table: [
        // {
        //     hour: '09',
        //     mon: [
        //         {
        //             status: "свободно",
        //             timeStart: "09:00",
        //             timeStop: "09:45",
        //             title: "Аквааэробика",
        //             coach: "Скала"
        //         }
        //     ],
        //     tue: [],
        //     wed: [],
        //     thu: [
        //         {
        //             status: "свободно",
        //             timeStart: "09:00",
        //             timeStop: "10:00",
        //             title: "Каратэ",
        //             coach: "Стетхем"
        //         },
        //         {
        //             status: "свободно",
        //             timeStart: "09:10",
        //             timeStop: "09:55",
        //             title: "Футбол",
        //             coach: "Зидан"
        //         }
        //     ],
        //     fri: [],
        //     sat: [],
        //     sun: [],
        // },
        // {
        //     hour: '11',
        //     mon: [],
        //     tue: [
        //         {
        //             status: "свободно",
        //             timeStart: "11:00",
        //             timeStop: "11:55",
        //             title: "Хайфи",
        //             coach: "Кали Масл"
        //         }
        //     ],
        //     wed: [],
        //     thu: [],
        //     fri: [],
        //     sat: [],
        //     sun: [],
        // }
      ],

      phoneTable: [],

      secondName: "",
      chosenId: 0,

      showModal: false,
      showSecondName: false,

      forDelete: false,
      forUnBook: false,

      deviceWidth: 0,
      displayMobile: false,
    };
  },
  methods: {
    show: function (id, flag) {
      this.chosenId = id;
      if (flag == 2) this.forDelete = true;
      else if (flag == 3) this.forUnBook = true;
      this.showModal = true;
    },
    hide: function () {
      this.showModal = false;
      this.forDelete = false;
      this.forUnBook = false;
    },
    book: function () {
      let request = { id: this.chosenId, name: this.clientName, clientId: this.userId };
      if (this.showSecondName) {
        request.second = this.secondName;
      }
      axios
        .post("/api/trainings/book", request, {
          headers: {
            "Content-type": "application/json",
          },
        })
        .then((res) => {
          if (res.data.status) {
            this.load();
            this.hide();
            this.showSecondName = false;
          }
          alert(res.data.msg);
        });
    },
    unBook: function () {
        let request = { id: this.chosenId, name: this.clientName };
      if (this.showSecondName) {
        request.second = this.secondName;
      }
      axios
        .post("/api/trainings/unBook", request, {
          headers: {
            "Content-type": "application/json",
          },
        })
        .then((res) => {
          if (res.data.status) {
            this.load();
            this.hide();
            this.showSecondName = false;
          }
          alert(res.data.msg);
        });
    },
    pop: function () {
      axios
        .post(
          "/api/trainings/pop",
          { id: this.chosenId },
          {
            headers: {
              "Content-type": "application/json",
            },
          }
        )
        .then((res) => {
          if (res.data.status) {
            this.load();
            this.hide();
          }
          alert(res.data.msg);
        });
    },
    thisWeek: function () {
      let d = new Date();
      while (d.getDay() != 1) {
        d.setDate(d.getDate() - 1);
      }

      this.currentMonday = d;

      this.filter.startDate = moment(d).format("DD.MM.YYYY");

      let d2 = new Date();
      d2.setTime(d.getTime());

      let week = [];
      week.push(d2.getDate());
      while (d2.getDay() != 0) {
        d2.setDate(d2.getDate() + 1);
        week.push(d2.getDate());
      }
      this.filter.stopDate = moment(d2).format("DD.MM.YYYY");

      this.weekDays = week;
    },

    nextWeek() {
      this.currentMonday.setDate(this.currentMonday.getDate() + 7);

      this.filter.startDate = moment(this.currentMonday).format("DD.MM.YYYY");

      // let sunday = new Date();
      // sunday.setTime(this.currentMonday.getTime());
      // sunday.setDate(sunday.getDate() + 6);
      //
      // this.filter.stopDate = moment(sunday).format('DD.MM.YYYY');

      let d = new Date();
      d.setTime(this.currentMonday.getTime());

      let week = [];
      week.push(d.getDate());
      while (d.getDay() != 0) {
        d.setDate(d.getDate() + 1);
        week.push(d.getDate());
      }
      this.filter.stopDate = moment(d).format("DD.MM.YYYY");

      this.weekDays = week;
    },

    prevWeek() {
      this.currentMonday.setDate(this.currentMonday.getDate() - 7);

      this.filter.startDate = moment(this.currentMonday).format("DD.MM.YYYY");

      // let sunday = new Date();
      // sunday.setTime(this.currentMonday.getTime());
      // sunday.setDate(sunday.getDate() + 6);
      //
      // this.filter.stopDate = moment(sunday).format('DD.MM.YYYY');

      let d = new Date();
      d.setTime(this.currentMonday.getTime());

      let week = [];
      week.push(d.getDate());
      while (d.getDay() != 0) {
        d.setDate(d.getDate() + 1);
        week.push(d.getDate());
      }
      this.filter.stopDate = moment(d).format("DD.MM.YYYY");

      this.weekDays = week;
    },

    groupByHours(table) {
      let hours = [];
      let res = {};
      for (let i = 0; i < table.length; i++) {
        let hour = table[i].time_start.slice(0, 2);
        if (!hours.includes(hour)) {
          hours.push(hour);
          res[hour] = [];
        }
        res[hour].push(table[i]);
      }
      return res;
    },

    groupByDays(hour) {
      let res = {
        mon: [],
        tue: [],
        wed: [],
        thu: [],
        fri: [],
        sat: [],
        sun: [],
      };
      for (let i = 0; i < hour.length; i++) {
        let day = moment(hour[i].date, "YYYY-MM-DD").format("d");
        switch (day) {
          case "1":
            res.mon.push(hour[i]);
            break;
          case "2":
            res.tue.push(hour[i]);
            break;
          case "3":
            res.wed.push(hour[i]);
            break;
          case "4":
            res.thu.push(hour[i]);
            break;
          case "5":
            res.fri.push(hour[i]);
            break;
          case "6":
            res.sat.push(hour[i]);
            break;
          case "0":
            res.sun.push(hour[i]);
            break;
        }
      }
      return res;
    },

    setPhoneTable(table) {
      let days = this.groupByDays(table);
      let daysHours = [];
      Object.entries(days).forEach((day) => {
        let hours = [];
        let res = {};
        for (let i = 0; i < day[1].length; i++) {
          let hour = day[1][i].time_start.slice(0, 2);
          if (!hours.includes(hour)) {
            hours.push(hour);
            res[hour] = [];
          }
          res[hour].push(day[1][i]);
        }
        daysHours.push(res);
      });
      this.phoneTable = daysHours;

      // let days = [[], [], [], [], [], [], []];

      // table.forEach(item => {
      //     item.day = moment(item.date, 'YYYY-MM-DD').format('d');
      //     if (item.day === 0) {
      //         days[6].push(item);
      //     } else {
      //         days[item.day - 1].push(item);
      //     }
      //     item.hour = item.time_start.slice(0, 2);
      // });

      // days.forEach(day => {
      //     let hours = [];
      //     let res = {};

      // });
    },

    load() {
      let request = { coach: this.filter.coach };
      request.startDate = moment(this.filter.startDate, "DD.MM.YYYY").format(
        "YYYY-MM-DD"
      );
      request.stopDate = moment(this.filter.stopDate, "DD.MM.YYYY").format(
        "YYYY-MM-DD"
      );
      axios
        .post("/api/trainings/load", request, {
          headers: {
            "Content-type": "application/json",
          },
        })
        .then((res) => {
          if (res.data.status) {
            let table = res.data.table;
            this.setPhoneTable(table);
            let hours = this.groupByHours(table);
            table = [];
            for (let [key, value] of Object.entries(hours)) {
              let hour = this.groupByDays(value);
              hour["hour"] = key;
              table.push(hour);
            }

            table.sort(function (a, b) {
              return parseInt(a.hour) - parseInt(b.hour);
            });

            this.table = table;
          } else {
            alert(res.dats.msg);
          }
        });
    },

    cardColor(training) {
      if (training.coach_name == "Татьяна") {
        return " darkpink-color ";
      } else if (training.coach_name == "Азалия") {
        return " peach-color ";
      } else return " basic-color ";
    },
    scrollToElement(ref) {
      const el = document.getElementById(ref);
      if (el) {
        // Use el.scrollIntoView() to instantly scroll to the element
        el.scrollIntoView({ behavior: "smooth", alignToTop: true });
      }
    },
    updateWidth() {
      this.deviceWidth = window.innerWidth;
      if (this.deviceWidth < 1210) {
        this.displayMobile = true;
      } else this.displayMobile = false;
    },
  },
  watch: {
    filter: {
      deep: true,
      handler() {
        this.load();
      },
    },
  },
  computed: {},
  mounted() {
    this.thisWeek();
    this.load();
    this.updateWidth();
  },
  created() {
    window.addEventListener("resize", this.updateWidth);
  },
};
</script>

<style scoped>
</style>
