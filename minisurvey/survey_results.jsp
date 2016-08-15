<%@ page import="java.util.*" %>
<%@ include file="/header.jsp" %>
<jsp:useBean id="mainbar" class="MainBar" scope="request"/>
<jsp:useBean id="sectionbar" class="HomeBar" scope="request"/>
<%@ include file="/title.jsp" %>

<%= print_popup(session) %>
<H2>Current Surveys</H2>
<P>If you haven't voted already, <A HREF="/survey_form.jsp">vote on these surveys</A>.  Some feedback on
	my progress is helpful.
</P>
<%
		Survey survey = null;
		{
			SurveyFactory fact = new SurveyFactory(con, new Where("active='Y'"), new Orderby());
			fact.openCursor();
			boolean wereResults = false;
			while ( fact.next() ) {
				wereResults = true;
				survey = (Survey) fact.getObject();
				%>
				<A NAME="<%= survey.getSurvey() %>"></A>
				<%--<H3><%= survey.getQuestion() %></H3>--%>
				<%= formatSurveyResults(con, survey) %>
				<BR>
				<%
			}
			if ( ! wereResults ) {
				%><H3>No active surveys</H3><%
			}
			fact.closeCursor();
			fact = null;
		}
		%>

<HR>
<H2>Old Surveys</H2>
<%
	{
		SurveyFactory fact = new SurveyFactory(con, new Where("active='n' and isonline='Y'"), new Orderby("datein"));
		fact.openCursor();
		boolean wereRows = false;
		while ( fact.next() ) {
			wereRows = true;
			survey = (Survey) fact.getObject();
			%>
			<A NAME="<%= survey.getSurvey() %>"></A>
			<%--<H3><%= survey.getQuestion() %></H3>--%>
			<%= formatSurveyResults(con, survey) %>
			<BR>
			<%
		}
		fact.closeCursor();
		fact = null;
		if ( ! wereRows ) { 
			%><P>None</P><%
		}
	}
%>

<%@ include file="/foot.jsp"%> 
<%!
	java.text.SimpleDateFormat dateFormatter1 = new java.text.SimpleDateFormat ("MMM d, yyyy");

	String formatSurveyResults(Connection con, Survey survey) {

		StringBuffer o = new StringBuffer();

		PreparedStatement ps = null;
		ResultSet rs = null;
		PreparedStatement ps2 = null;
		ResultSet rs2 = null;
		try {
			String sql = "select count(1) from surveyres where survey = ?";
			ps = con.prepareStatement(sql);
			ps.setString(1, survey.getSurvey());
			rs = ps.executeQuery();
			rs.next();
			int answerCount = rs.getInt(1);
			rs.close();
			ps.close();

			sql = "select surveya, answer from surveya where survey = ? order by orderby";
			ps = con.prepareStatement(sql);
			ps.setString(1, survey.getSurvey());
			rs = ps.executeQuery();

			o.append("<TABLE CELLPADDING=5 CELLSPACING=1 BORDER=0 BGCOLOR=\"#eeeeee\" WIDTH=\"500\">\n");
			o.append("<TR><TD COLSPAN=4 BGCOLOR=\"#333333\"><FONT COLOR=\"#eeeeee\"><B>");
			o.append(survey.getQuestion() );
			o.append("</B><BR>");
			if ( survey.getDateoffline() == null ) {
				o.append(" (Survey&nbsp;started:&nbsp;");
				o.append(dateFormatter1.format(survey.getDatein()) );
				o.append(")");
			}
			else {
				o.append(" (Survey&nbsp;ran:&nbsp;");
				o.append(dateFormatter1.format(survey.getDatein()) );
				o.append("&nbsp;through&nbsp;");
				o.append(dateFormatter1.format(survey.getDateoffline()) );
				o.append(")");
			}
			o.append("</FONT></TD></TR>\n");
			o.append("<TR BGCOLOR=\"#cccccc\">\n");
			o.append("   <TH>Answer</TH>\n");
			o.append("   <TH>Votes</TH>\n");
			o.append("   <TH>Percentage</TH>\n");
			o.append("   <TH>Graph</TH>\n");
			o.append("</TR>\n");
			while ( rs.next() ) {
				String surveyaPK = rs.getString(1);
				String answer = rs.getString(2);

				sql = "select count(1), round(( count(1) / ?) * 100) from surveyres " +
					"where surveya = ? group by surveya";
				ps2 = con.prepareStatement(sql);
				ps2.setInt(1,answerCount);
				ps2.setString(2,surveyaPK);
				rs2 = ps2.executeQuery();
				int width = 0;
				int thisanswercount = 0;
				int thisanswerratio = 0;
				if ( rs2.next() ) {
					thisanswercount = rs2.getInt(1);
					thisanswerratio = rs2.getInt(2);
					double ratio = (double)thisanswerratio / 100.00;
					int totalWidth = 150;
					width = (int) ( totalWidth * ratio );
				}
				width++;

				o.append("<TR>");
				o.append("<TD>");
				o.append(answer);
				o.append("</TD>");
				o.append("<TD ALIGN=RIGHT>");
				o.append(thisanswercount);
				o.append("</TD>");
				o.append("<TD ALIGN=RIGHT>");
				o.append(thisanswerratio);
				o.append("%</TD>");
				o.append("<TD><IMG SRC=\"/images/dot_blue.gif\" HEIGHT=\"12\" WIDTH=\"");
				o.append(width);
				o.append("\"></TD>");
				o.append("</TR>\n");
			}
			o.append("</TABLE>\n");
		}
		catch ( SQLException e ) {
			throw new RuntimeException(e.getMessage());
		}
		finally {
			try {
				if ( rs != null ) { rs.close(); }
				if ( ps != null ) { ps.close(); }
				if ( rs2 != null ) { rs2.close(); }
				if ( ps2 != null ) { ps2.close(); }
			}
			catch ( SQLException e ) {
				throw new RuntimeException(e.getMessage());
			}
		}
		return o.toString();

	}
%>

