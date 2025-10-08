<style>
    .policy-dropdown-menu {
	left: calc(100% + 24px);
	top: -25px;
	padding: 25px;
	margin: 0;
	width: 360px;
	border: none;
	background: transparent;
	/* overflow: hidden; */
	border-radius: 0;
	-webkit-border-radius: 0;
	-moz-border-radius: 0;
	-ms-border-radius: 0;
	-o-border-radius: 0;
}

.policy-dropdown-menu:before {
	position: absolute;
	content: "";
	left: 0;
	right: 0;
	top: 50%;
	width: 100%;
	height: 60000px;
	box-shadow: 4px 0 4px 0 rgba(0, 0, 0, 0.03);
	z-index: -1;
	background: var(--white);
	transform: translate(0, -50%);
	-webkit-transform: translate(0, -50%);
	-moz-transform: translate(0, -50%);
	-ms-transform: translate(0, -50%);
	-o-transform: translate(0, -50%);
}

.policy-dropdown-menu {
	position: unset;
	width: 100%;
	left: auto;
	top: auto;
	margin: 0;
	padding: 12px 38px;
	border: none;
	transform: none;
	-webkit-transform: none;
	-moz-transform: none;
	-ms-transform: none;
	-o-transform: none;
}

.policy-dropdown-menu:before {
	display: none;
}

.policy-dropdown-menu>li {
	margin-bottom: 12px;
}

.policy-dropdown-menu>li>a {
	display: inline-block;
	font-family: 'SourceSansProRegular', sans-serif;
	width: auto;
	/* min-width: 106px; */
	font-size: 16px;
	line-height: 110%;
	color: #837E7E;
	padding: 3px 10px !important;
	text-transform: capitalize;
	background: transparent;
	border-radius: 6px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	-ms-border-radius: 6px;
	-o-border-radius: 6px;
}

.policy-dropdown-menu>li>a:hover {
	color: var(--red);
	background: rgba(171, 24, 45, 0.12)!important;
}

.policy-dropdown-menu>li>a.show,
.policy-dropdown-menu>li.active>a,
.policy-dropdown-menu>li.current-menu-item>a,
.policy-dropdown-menu>li.current-menu-parent>a,
.policy-dropdown-menu>li.current_page_parent>a,
.policy-dropdown-menu>li>a.active,
.policy-dropdown-menu>li>a.current-menu-item,
.policy-dropdown-menu>li>a.current-menu-parent,
.policy-dropdown-menu>li>a.current_page_parent {
	font-family: 'SourceSansProSemibold', sans-serif;
	color: var(--red);
	background: rgba(171, 24, 45, 0.12)!important;
}




label.error {
	color: #dc3545;
	font-size: 15px;
	margin-top: 5px;
}





li .policy-dropdown-menu .policy-dropdown-item {
	background-color: transparent!important;
	font-family: 'SourceSansProRegular';
	font-size: 16px;
	font-style: normal;
	line-height: normal;
	padding: 2px 9px;
}

li .policy-dropdown-menu .policy-dropdown-item:hover {
	background: rgba(171, 24, 45, 0.12)!important;
	color: var(--red)!important;
    cursor: pointer!important;
	border-radius: 6px;
	font-family: 'SourceSansProSemibold';
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	-ms-border-radius: 6px;
	-o-border-radius: 6px;
}

/* insured css */
.insured-life-item {
	margin-bottom: 8px;
}

.insured-life-link {
	display: flex !important;
	justify-content: space-between !important;
	align-items: center !important;
	padding: 8px 12px !important;
	background: #f8f9fa !important;
	border: 1px solid #e9ecef !important;
	border-radius: 4px !important;
	text-decoration: none !important;
	color: #495057 !important;
	font-size: 14px !important;
	transition: all 0.2s ease !important;
}

.insured-life-link:hover {
	background: #e9ecef !important;
	border-color: #adb5bd !important;
	text-decoration: none !important;
	color: #495057 !important;
}

.insured-name {
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-right: 8px;
}

.insured-actions {
	display: flex;
	gap: 4px;
	opacity: 0.7;
	transition: opacity 0.2s ease;
}

.insured-life-link:hover .insured-actions {
	opacity: 1;
}

.insured-actions .btn {
	padding: 2px 6px;
	font-size: 12px;
	border-radius: 3px;
	border: none;
}

.insured-actions .edit-insured {
	background-color: #007bff;
	color: white;
}

.insured-actions .edit-insured:hover {
	background-color: #0056b3;
}

.insured-actions .delete-insured {
	background-color: #dc3545;
	color: white;
}

.insured-actions .delete-insured:hover {
	background-color: #c82333;
}

.insured-lives-submenu {
	margin-top: 8px;
}

.insured-lives-submenu .insured-life-item:last-child {
	margin-bottom: 0;
}

.insured-life-link .fas {
	font-size: 11px;
}

.policy-dropdown-menu .insured-life-item {
	list-style: none;
}

.policy-dropdown-menu .insured-life-item a {
	text-decoration: none;
}

/* insured css */
/* ben css */
.beneficiary-item {
	margin-bottom: 8px;
}

.beneficiary-link {
	display: flex !important;
	justify-content: space-between !important;
	align-items: center !important;
	padding: 8px 12px !important;
	background: #f8f9fa !important;
	border: 1px solid #e9ecef !important;
	border-radius: 4px !important;
	text-decoration: none !important;
	color: #495057 !important;
	font-size: 14px !important;
	transition: all 0.2s ease !important;
}

.beneficiary-link:hover {
	background: #e9ecef !important;
	border-color: #adb5bd !important;
	text-decoration: none !important;
	color: #495057 !important;
}

.beneficiary-name {
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-right: 8px;
}

.beneficiary-actions {
	display: flex;
	gap: 4px;
	opacity: 0.7;
	transition: opacity 0.2s ease;
}

.beneficiary-link:hover .beneficiary-actions {
	opacity: 1;
}

.beneficiary-actions .btn {
	padding: 2px 6px;
	font-size: 12px;
	border-radius: 3px;
	border: none;
}

.beneficiary-actions .edit-beneficiary {
	background-color: #007bff;
	color: white;
}

.beneficiary-actions .edit-beneficiary:hover {
	background-color: #0056b3;
}

.beneficiary-actions .delete-beneficiary {
	background-color: #dc3545;
	color: white;
}

.beneficiary-actions .delete-beneficiary:hover {
	background-color: #c82333;
}

.beneficiary-submenu {
	margin-top: 8px;
}

.beneficiary-submenu .beneficiary-item:last-child {
	margin-bottom: 0;
}

.beneficiary-link .fas {
	font-size: 11px;
}

.policy-dropdown-menu .beneficiary-item {
	list-style: none;
}

.policy-dropdown-menu .beneficiary-item a {
	text-decoration: none;
}
/* ben css */


/* introducer css */
.introducer-item {
	margin-bottom: 8px;
}

.introducer-link {
	display: flex !important;
	justify-content: space-between !important;
	align-items: center !important;
	padding: 8px 12px !important;
	background: #f8f9fa !important;
	border: 1px solid #e9ecef !important;
	border-radius: 4px !important;
	text-decoration: none !important;
	color: #495057 !important;
	font-size: 14px !important;
	transition: all 0.2s ease !important;
}

.introducer-link:hover {
	background: #e9ecef !important;
	border-color: #adb5bd !important;
	text-decoration: none !important;
	color: #495057 !important;
}

.introducer-name {
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-right: 8px;
}

.introducer-actions {
	display: flex;
	gap: 4px;
	opacity: 0.7;
	transition: opacity 0.2s ease;
}

.introducer-link:hover .introducer-actions {
	opacity: 1;
}

.introducer-actions .btn {
	padding: 2px 6px;
	font-size: 12px;
	border-radius: 3px;
	border: none;
}

.introducer-actions .edit-introducer {
	background-color: #007bff;
	color: white;
}

.introducer-actions .edit-introducer:hover {
	background-color: #0056b3;
}

.introducer-actions .delete-introducer {
	background-color: #dc3545;
	color: white;
}

.introducer-actions .delete-introducer:hover {
	background-color: #c82333;
}

.introducer-lives-submenu {
	margin-top: 8px;
}

.introducer-lives-submenu .introducer-life-item:last-child {
	margin-bottom: 0;
}

.introducer-life-link .fas {
	font-size: 11px;
}

.policy-dropdown-menu .introducer-life-item {
	list-style: none;
}

.policy-dropdown-menu .introducer-life-item a {
	text-decoration: none;
}

/* introducer css */


/* policyholder css */
.policyholder-item {
	margin-bottom: 8px;
}

.policyholder-link {
	display: flex !important;
	justify-content: space-between !important;
	align-items: center !important;
	padding: 8px 12px !important;
	background: #f8f9fa !important;
	border: 1px solid #e9ecef !important;
	border-radius: 4px !important;
	text-decoration: none !important;
	color: #495057 !important;
	font-size: 14px !important;
	transition: all 0.2s ease !important;
}

.policyholder-link:hover {
	background: #e9ecef !important;
	border-color: #adb5bd !important;
	text-decoration: none !important;
	color: #495057 !important;
}

.policyholder-name {
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-right: 8px;
}

.policyholder-actions {
	display: flex;
	gap: 4px;
	opacity: 0.7;
	transition: opacity 0.2s ease;
}

.policyholder-link:hover .policyholder-actions {
	opacity: 1;
}

.policyholder-actions .btn {
	padding: 2px 6px;
	font-size: 12px;
	border-radius: 3px;
	border: none;
}

.policyholder-actions .edit-policyholder {
	background-color: #007bff;
	color: white;
}

.policyholder-actions .edit-policyholder:hover {
	background-color: #0056b3;
}

.policyholder-actions .delete-policyholder {
	background-color: #dc3545;
	color: white;
}

.policyholder-actions .delete-policyholder:hover {
	background-color: #c82333;
}

.policyholder-lives-submenu {
	margin-top: 8px;
}

.policyholder-lives-submenu .policyholder-life-item:last-child {
	margin-bottom: 0;
}

.policyholder-life-link .fas {
	font-size: 11px;
}

.policy-dropdown-menu .policyholder-life-item {
	list-style: none;
}

.policy-dropdown-menu .policyholder-life-item a {
	text-decoration: none;
}

/* policyholder css */

li .policy-dropdown-menu.show {
	position: unset !important;
	transform: none !important;
	border: none;
	padding: 10px 32px 12px;
	-webkit-transform: none !important;
	-moz-transform: none !important;
	-ms-transform: none !important;
	-o-transform: none !important;
}

li .policy-dropdown-menu li:not(:last-child) {
	margin-bottom: 12px;
}

.policy-dropdown-menu li,
.sidebar-main-menu .navbar .navbar-nav .nav-item {
	margin-bottom: 10px;
}

.sidebar-main-menu .navbar .navbar-nav .nav-item .nav-link {
	font-family: 'SourceSansProRegular', sans-serif;
	font-size: 18px;
	line-height: 125%;
	color: #837E7E;
	padding: 7px 23px;
	background: #F5F5F5;
	text-transform: capitalize;
	box-shadow: none;
	border-radius: 6px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	-ms-border-radius: 6px;
	-o-border-radius: 6px;
}

.policy-dropdown-menu li>a {
	font-family: 'SourceSansProRegular', sans-serif;
	font-size: 18px;
	line-height: 125%;
	color: #837E7E;
	padding: 7px 23px;
	background: #F5F5F5;
	text-transform: capitalize;
	border-radius: 6px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	-ms-border-radius: 6px;
	-o-border-radius: 6px;
}

.policy-dropdown-menu .dropdown>a,
.sidebar-main-menu .navbar .navbar-nav>.dropdown>a {
	position: relative;
	display: flex;
	flex-wrap: wrap;
	word-wrap: break-word;
	white-space: normal;
	align-items: center;
	justify-content: space-between;
	padding-right: 40px !important;
}

.dropdown>a:after {
	position: absolute;
	right: 23px;
	top: 50%;
	border: none;
	width: 7px;
	height: 14px;
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='9' height='16' viewBox='0 0 9 16' fill='none'%3E%3Cpath d='M1.33087 15.2402L8.05524 8.51579L1.33087 1.72547' stroke='%23837E7E' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat;
	background-size: 100% 100%;
	transition: all 0.3s ease-in-out;
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	-ms-transition: all 0.3s ease-in-out;
	-o-transition: all 0.3s ease-in-out;
	transform: translate(0, -50%);
	-webkit-transform: translate(0, -50%);
	-moz-transform: translate(0, -50%);
	-ms-transform: translate(0, -50%);
	-o-transform: translate(0, -50%);
}

.policy-dropdown-menu .dropdown>a:after {
	position: absolute;
	right: 23px;
	top: 50%;
	border: none;
	width: 7px;
	height: 14px;
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='9' height='16' viewBox='0 0 9 16' fill='none'%3E%3Cpath d='M1.33087 15.2402L8.05524 8.51579L1.33087 1.72547' stroke='%23837E7E' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat;
	background-size: 100% 100%;
	transition: all 0.3s ease-in-out;
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	-ms-transition: all 0.3s ease-in-out;
	-o-transition: all 0.3s ease-in-out;
	transform: translate(0, -50%) rotate(90deg);
	-webkit-transform: translate(0, -50%) rotate(90deg);
	-moz-transform: translate(0, -50%) rotate(90deg);
	-ms-transform: translate(0, -50%) rotate(90deg);
	-o-transform: translate(0, -50%) rotate(90deg);
}

.policy-dropdown-menu .dropdown>a.show:after {
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='9' height='16' viewBox='0 0 9 16' fill='none'%3E%3Cpath d='M1.33087 15.2402L8.05524 8.51579L1.33087 1.72547' stroke='%23AB182D' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat;
	background-size: 100% 100%;
	transform: translate(0, -50%) rotate(-90deg);
	-webkit-transform: translate(0, -50%) rotate(-90deg);
	-moz-transform: translate(0, -50%) rotate(-90deg);
	-ms-transform: translate(0, -50%) rotate(-90deg);
	-o-transform: translate(0, -50%) rotate(-90deg);
}

.policy-dropdown-menu .dropdown>a.show {
	font-family: "SourceSansProSemibold", sans-serif;
	color: var(--red);
}

.new-case {
    padding-right: 100px;
}

.case-management .policy-dropdown-toggle {
    padding: 8px 20px!important;
}

.case-management .policy-dropdown-submenu {
    padding: 8px 20px!important;
}

li {
	list-style: none!important;
}

#form-section-a-2 input, #form-section-a-2 textarea {
	/* width: 90%!important; */
}

div.iti--inline-dropdown {
	margin-top: 13px!important;
    left: 2px!important;
    width: 100%!important;
}

</style>



<style>
	/* Spinner for save */

	.save-indicator {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 8px 16px;
		border-radius: 20px;
		font-size: 13px;
		font-weight: 400;
		transition: all 0.3s ease;
		min-width: 120px;
		justify-content: center;
		position: relative;
		overflow: hidden;
	}

	/* Saving State */
	.save-indicator.saving {
		background-color: #fff;
		color: #5f6368;
		border: 1px solid #dadce0;
	}

	.save-indicator.saving::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(66, 133, 244, 0.1), transparent);
		animation: shimmer 1.5s infinite;
	}

	/* Saved State */
	.save-indicator.saved {
		background-color: #e8f5e8;
		color: #137333;
		border: 1px solid #c6e5c6;
	}

	/* Error State */
	.save-indicator.error {
		background-color: #fce8e6;
		color: #d93025;
		border: 1px solid #f9ab9f;
	}

	/* Spinner Animation */
	.spinner {
		width: 16px;
		height: 16px;
		border: 2px solid #e8eaed;
		border-top: 2px solid #4285f4;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	.checkmark {
		width: 16px;
		height: 16px;
		fill: #137333;
	}

	.error-icon {
		width: 16px;
		height: 16px;
		fill: #d93025;
	}

	/* Animations */
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	@keyframes shimmer {
		0% { left: -100%; }
		100% { left: 100%; }
	}

	@keyframes fadeIn {
		0% { opacity: 0; transform: translateY(-5px); }
		100% { opacity: 1; transform: translateY(0); }
	}

	@keyframes pulse {
		0%, 100% { transform: scale(1); }
		50% { transform: scale(1.05); }
	}

	.save-indicator.saved {
		animation: fadeIn 0.3s ease, pulse 0.6s ease 0.3s;
	}

	.save-indicator.error {
		animation: fadeIn 0.3s ease;
	}	
</style>