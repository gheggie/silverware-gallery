<% if $FooterShown %>
  <footer>
    <% include Button Tag='a', HREF=$PrevLink, Icon='chevron-left', Text=$PrevText %>
    <% include Button Tag='a', HREF=$NextLink, Icon='chevron-right', Text=$NextText %>
  </footer>
<% end_if %>
