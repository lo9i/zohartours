
    <div class='ui segment'>
        <div class="ui dimmer">
            <div class="ui text loader">Changing the dates.</div>
        </div>
        <div class='ui three column grid align-center'>
            <div class='column'>
                <button class='ui left labeled icon mini button clndr-previous-button'>
                    <i class='left arrow icon'></i> Prev
                </button>
            </div>
            <div class='column'>
                <h5 class='ui header'><%= month %>, <%= year %></h5>
            </div>
            <div class='column'>
                <button class='ui right labeled icon mini button clndr-next-button'>
                    <i class='right arrow icon'></i> Next
                </button>
            </div>
        </div>
        <div class='ui segment calendar-segment'>
            <table class='ui celled table align-center stopsell-calendar'>
                <thead>
                    <tr class='header-days'>
                        <% _.each(daysOfTheWeek, function(day) { %>
                          <th class='header-day'><%= day %></th>
                        <% }); %>
                    </tr>
                </thead>
                <tbody>
                    <% _.each(days, function(day) { %>
                        <% if (day.classes.indexOf('calendar-dow-0') > -1 ) { %>
                            <tr>
                        <% }; %>

                        <td class='<%= day.classes %>'>
                            <span class='ui circular label'><%= day.day %></span>
                        </td>

                        <% if (day.classes.indexOf('calendar-dow-6') > -1 ) { %>
                            </tr>
                        <% }; %>

                    <% }); %>
                </tbody>
            </table>
        </div>
    </div>