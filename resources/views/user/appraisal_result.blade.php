<?php
use App\Models\AppraisalQuestion;
use App\Models\AppraisalAnswer;
?>
@extends('layouts.auth')
@section('title')
{{$user->first_name}} {{$user->last_name}} | {{$form->form_name}}
@endsection

@section('content')

<div>

<div class="header">
    <div class="headerContainer">
    <img src="{{asset('images/icons/icon-72x72.png') }}"/>
    <h3>Whence Financial Services</h3>
    <p style="font-weight: bold; font-size: 15px; text-decoration: underline;">{{$form->form_name}}<p>
    </div>
</div>

<div>
    
</div>
<div class="main2">


<div class="main3">
@foreach($sections as $section)
<?php
$questions = AppraisalQuestion::where('form_id',$form->id)->where('section_id',$section->id)->get();
?>
<h3>{{$section->section_name}}</h3>
    <div class="infobox2">

    @foreach($questions as $question)
    @if(!in_array($question->unit, ['p_r', 'sb_r', 'p_r_dm', 'rr_r', 'ma_r', 'rh_r']))
        <p style="font-weight:bold; font-size:20px;">Question: {{$question->question}}</p>
    @if(App\Models\AppraisalAnswer::where('section_id',$section->id)->where('question_id',$question->id)->where('user_id',$user->id)->where('form_id',$form->id)->first() != null)
    <p style="font-size: 18px;">Answer: <span style="text-decoration: underline; font-size:18px;">{{ App\Models\AppraisalAnswer::where('section_id',$section->id)->where('question_id',$question->id)->where('user_id',$user->id)->where('form_id',$form->id)->first()->answer;}}</span></p>
    @endif
    @elseif($question->unit == 'p_r')
    <p style="font-weight:bold; font-size:20px;">{{$question->question}}</p>
    @foreach($peers as $peer)
    <?php
    $ans = AppraisalAnswer::where('question_id',$question->id)->where('user_id',$peer->id)->get();
    $average = 0;
    $count = 0.001;
    $sum = 0;

    foreach($ans as $an){
        $count = $count + 1;
        $sum = $sum + $an->answer;
    }

    $average = $sum/$count;
  //  array_push($pr_answers,$average);
    ?>
    <p style="font-size: 18px;">{{$peer->first_name}} {{$peer->last_name}}: <span style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</span></p>
    @endforeach

    @elseif($question->unit == 'sb_r')
    <p style="font-weight:bold; font-size:20px;">{{$question->question}}</p>
    @foreach($managers as $peer)
    <?php
    $ans = AppraisalAnswer::where('question_id',$question->id)->where('user_id',$peer->id)->get();
    $average = 0;
    $count = 0.001;
    $sum = 0;

    foreach($ans as $an){
        $count = $count + 1;
        $sum = $sum + $an->answer;
    }

    $average = $sum/$count;
  //  array_push($pr_answers,$average);
    ?>
    <p style="font-size: 18px;">{{$peer->first_name}} {{$peer->last_name}}: <span style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</span></p>
    @endforeach

    @elseif($question->unit == 'rr_r')
    <p style="font-weight:bold; font-size:20px;">{{$question->question}}</p>
    @foreach($recoveries_reps as $peer)
    <?php
    $ans = AppraisalAnswer::where('question_id',$question->id)->where('user_id',$peer->id)->get();
    $average = 0;
    $count = 0.001;
    $sum = 0;

    foreach($ans as $an){
        $count = $count + 1;
        $sum = $sum + $an->answer;
    }

    $average = $sum/$count;
  //  array_push($pr_answers,$average);
    ?>
    <p style="font-size: 18px;">{{$peer->first_name}} {{$peer->last_name}}: <span style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</span></p>
    @endforeach


    @elseif($question->unit == 'rh_r')
    <p style="font-weight:bold; font-size:20px;">{{$question->question}}</p>
    @foreach($recoveries_head as $peer)
    <?php
    $ans = AppraisalAnswer::where('question_id',$question->id)->where('user_id',$peer->id)->get();
    $average = 0;
    $count = 0.001;
    $sum = 0;

    foreach($ans as $an){
        $count = $count + 1;
        $sum = $sum + $an->answer;
    }

    $average = $sum/$count;
  //  array_push($pr_answers,$average);
    ?>
    <p style="font-size: 18px;">{{$peer->first_name}} {{$peer->last_name}}: <span style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</span></p>
    @endforeach


    @elseif($question->unit == 'ma_r')
    <p style="font-weight:bold; font-size:20px;">{{$question->question}}</p>
    @foreach($manager_admin as $peer)
    <?php
    $ans = AppraisalAnswer::where('question_id',$question->id)->where('user_id',$peer->id)->get();
    $average = 0;
    $count = 0.001;
    $sum = 0;

    foreach($ans as $an){
        $count = $count + 1;
        $sum = $sum + $an->answer;
    }

    $average = $sum/$count;
  //  array_push($pr_answers,$average);
    ?>
    <p style="font-size: 18px;">{{$peer->first_name}} {{$peer->last_name}}: <span style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</span></p>
    @endforeach

    @endif
    @endforeach
    </div>
@endforeach



<h3>My Reviews</h3>
<div class="infobox2">

@if($user->role->role_id == 4 || $user->role->role_id == 6)
<?php
$qs = AppraisalQuestion::where('unit','sb_r')->get();
?>
@foreach($qs as $q)
<p style="font-weight:bold; font-size:20px;">{{$q->question}}</p>
<?php


  $ans = AppraisalAnswer::where('question_id',$q->id)->where('user_id',$user->id)->get();
  $average = 0;
  $count = 0.001;
  $sum = 0;

  foreach($ans as $an){
      $count = $count + 1;
      $sum = $sum + $an->answer;
  }

  $average = $sum/$count;

?>
@if($average == 0)
<p style="text-decoration: underline; font-size:18px;">N/A</p>
@else
<p style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</p>
@endif
@endforeach


@endif



@if($user->dual_role != null)


@if($user->dual_role->role_id == 8)
<?php
$qs = AppraisalQuestion::where('unit','rh_r')->get();
?>
@foreach($qs as $q)
<p style="font-weight:bold; font-size:20px;">{{$q->question}}</p>
<?php


  $ans = AppraisalAnswer::where('question_id',$q->id)->where('user_id',$user->id)->get();
  $average = 0;
  $count = 0.001;
  $sum = 0;

  foreach($ans as $an){
      $count = $count + 1;
      $sum = $sum + $an->answer;
  }

  $average = $sum/$count;

?>
@if($average == 0)
<p style="text-decoration: underline; font-size:18px;">N/A</p>
@else
<p style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</p>
@endif

@endforeach


@elseif($user->dual_role->role_id == 9)
<?php
$qs = AppraisalQuestion::where('unit','ma_r')->get();
?>
@foreach($qs as $q)
<p style="font-weight:bold; font-size:20px;">{{$q->question}}</p>
<?php


  $ans = AppraisalAnswer::where('question_id',$q->id)->where('user_id',$user->id)->get();
  $average = 0;
  $count = 0.001;
  $sum = 0;

  foreach($ans as $an){
      $count = $count + 1;
      $sum = $sum + $an->answer;
  }

  $average = $sum/$count;

?>
@if($average == 0)
<p style="text-decoration: underline; font-size:18px;">N/A</p>
@else
<p style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</p>
@endif
@endforeach


@elseif($user->dual_role->role_id == 7)


<?php
$qs = AppraisalQuestion::where('unit','rr_r')->get();
?>
@foreach($qs as $q)
<p style="font-weight:bold; font-size:20px;">{{$q->question}}</p>
<?php


  $ans = AppraisalAnswer::where('question_id',$q->id)->where('user_id',$user->id)->get();
  $average = 0;
  $count = 0.001;
  $sum = 0;

  foreach($ans as $an){
      $count = $count + 1;
      $sum = $sum + $an->answer;
  }

  $average = $sum/$count;

?>
@if($average == 0)
<p style="text-decoration: underline; font-size:18px;">N/A</p>
@else
<p style="text-decoration: underline; font-size:18px;"> <span  style="text-decoration: underline; font-size:22px; font-weight:bold; ">{{number_format($average)}}</span>/5</p>
@endif
@endforeach


@endif

@endif






</div>




</div>

</div>

</div>
@endsection

@section('footer-scripts')
<style>
    ul.a {
  list-style-type: circle;
}

    .header {
  overflow: hidden;
  padding: 10px 10px;

}
.headerContainer{
    flex-direction: column;
    display: flex;
    justify-content: center;
    align-items: center;
    

}

.main{
    display: flex;
    justify-content: center;
    align-items: center;
}

.infobox{
    flex-direction: column;
    align-items: center;
    display: flex;
    justify-content: center;
    border-width: thin;
    width: 80%;
    border-style: solid;
    padding: 20px;
    background-color: #f2f2f2;
}

.infobox2{
    border-width: thin;
    width: 100%;
    border-style: solid;
    padding: 20px;
    background-color: #f2f2f2;

}

.main2{
    display: flex;
    justify-content: center;
    align-items: center;
    border-width: thin;
}

.main3{
    width: 70%;
}


.startButton{
    background-color: #04AA6D; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;

}

</style>
@endsection
