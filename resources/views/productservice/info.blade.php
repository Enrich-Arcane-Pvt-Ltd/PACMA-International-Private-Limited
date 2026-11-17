<style>
    #new_container{
  font-family: 'open sans';
  overflow-x: hidden; }

img {
  max-width: 100%;
 
 }

.preview {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
      -ms-flex-direction: column;
          flex-direction: column; }
  @media screen and (max-width: 996px) {
    .preview {
      margin-bottom: 50px; } }

.preview-pic {
  -webkit-box-flex: 1;
  -webkit-flex-grow: 1;
      -ms-flex-positive: 2;
          flex-grow: 2; }

.preview-thumbnail.nav-tabs {
  border: none;
  margin-top: 15px; }
  .preview-thumbnail.nav-tabs li {
    width: 18%;
    margin-right: 2.5%; }
    .preview-thumbnail.nav-tabs li img {
      max-width: 100%;
      display: block; }
    .preview-thumbnail.nav-tabs li a {
      padding: 0;
      margin: 0; }
    .preview-thumbnail.nav-tabs li:last-of-type {
      margin-right: 0; }

.tab-content {
  overflow: hidden; }
  .tab-content img {
    width: 100%;
    -webkit-animation-name: opacity;
            animation-name: opacity;
    -webkit-animation-duration: .3s;
            animation-duration: .3s; }

#new_card {
  margin-top: 50px;
  background: #D8DBEE;
  padding: 3em;
  line-height: 1.5em; }

@media screen and (min-width: 997px) {
  .wrapper {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex; } }

.details {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
      -ms-flex-direction: column;
          flex-direction: column; }

.colors {
  -webkit-box-flex: 1;
  -webkit-flex-grow: 1;
      -ms-flex-positive: 1;
          flex-grow: 1; }

.product-title,.sizes, .colors {
  text-transform: UPPERCASE;
  font-weight: bold; }

  .price{
   
  font-weight: bold;}
 
 


.checked, .price span {
  color: #ff9f1a; }

.product-title, .rating, .product-description, .price, .vote, .sizes {
  margin-bottom: 15px; }

.product-title {
  margin-top: 0; }

.size {
  margin-right: 10px; }
  .size:first-of-type {
    margin-left: 40px; }

.color {
  display: inline-block;
  vertical-align: middle;
  margin-right: 10px;
  height: 2em;
  width: 2em;
  border-radius: 2px; }
  .color:first-of-type {
    margin-left: 20px; }

.add-to-cart, .like {
  background: #ff9f1a;
  padding: 1.2em 1.5em;
  border: none;
  text-transform: UPPERCASE;
  font-weight: bold;
  color: #fff;
  -webkit-transition: background .3s ease;
          transition: background .3s ease; }
  .add-to-cart:hover, .like:hover {
    background: #b36800;
    color: #fff; }

.not-available {
  text-align: center;
  line-height: 2em; }
  .not-available:before {
    font-family: fontawesome;
    content: "\f00d";
    color: #fff; }

#details{
  margin-bottom: 15px;
}
.orange {
  background: #ff9f1a; }

.green {
  background: #85ad00; }

.blue {
  background: #0076ad; }

.tooltip-inner {
  padding: 1.3em; }

@-webkit-keyframes opacity {
  0% {
    opacity: 0;
    -webkit-transform: scale(3);
            transform: scale(3); }
  100% {
    opacity: 1;
    -webkit-transform: scale(1);
            transform: scale(1); } }

@keyframes opacity {
  0% {
    opacity: 0;
    -webkit-transform: scale(3);
            transform: scale(3); }
  100% {
    opacity: 1;
    -webkit-transform: scale(1);
            transform: scale(1); } }
</style>
  <div class="container" id="new_container">
		<div class="card" id="new_card">
			<div class="container-fliud">
				<div class="wrapper row">
					<div class="preview col-md-6">
           <div class="row">
						<div class="preview-pic tab-content">
                  <div class="tab-pane active" id="pic-1"><img src="{{$productService->image_1}}" /></div>
                  <div class="tab-pane" id="pic-2"><img src="{{$productService->image_2}}"/></div>
                  <div class="tab-pane" id="pic-3"><img src="{{$productService->image_3}}" /></div>   
						</div>
						<ul class="preview-thumbnail nav nav-tabs">
                  <li class="active"><a data-target="#pic-1" data-toggle="tab"><img src="{{$productService->image_1}}" /></a></li>
                  <li><a data-target="#pic-2" data-toggle="tab"><img src="{{$productService->image_2}}" /></a></li>
                  <li><a data-target="#pic-3" data-toggle="tab"><img src="{{$productService->image_3}}" /></a></li>  
						</ul>
          </div>
            <br></br>
          <div class="row">	
            <h9 class="vote"><strong><u>Other Details</u></strong></h9> 
          </div>  
          <div class="row">	
            <br></br>       
            
            <p  class="price">Warranty Period - <span>{{$productService->warranty_period}}</span></p>
            <br></br>
          </div>
          <div class="row">
            <h10 class="price">Additional Information - </h10>
            <p>{{$productService->additional_details}}</p>
          </div>
          <br></br>
          <div class="row">
            <p class="vote"><strong>Down Payment (%) -</strong></p>
          </div>
          <div class="row">
            <p class="vote"><strong>Interest (%)     -</strong></p>	
           </div>
            
					</div>
					<div class="details col-md-6">
            
						<h3 class="product-title">{{$productService->name}}</h3>
						<p  class="price">Price: <span>Rs.{{$productService->sale_price}}.00</span></p>
            <p class="product-description">{{$productService->description}}</p>
            
            
           
					</div>
				</div>
			</div>
		</div>
	</div>
    