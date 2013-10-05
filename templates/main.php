{{ script('main') }}
{{ style('main') }}
{{ script('date')}}
{{ script('md5') }}
{{ script('wjl') }}
<div id="app" >
	<div id="undo-container">
	</div>
    <div id="app-navigation" >
        <ul id="conversations">
            <!--  All active conversation for this user are displayed here as a list-->
		</ul>

		<div id="app-settings" >
            <!-- The user can join or create a conversation here -->
            <fieldset >   
                <input  type="text" id="user" placeholder="User Name"><br>
                <button  type="submit" id="createConverstation" >Create Conversation</button>
            </fieldset>
            <ul id="status">
            
            </ul>
        </div>    

	</div>

	<div id="app-content" >
        <div id="chats">
        </div>
        
        
    </div>
</div>
