import swimmer from "./swimming.png";
export default function Splash() {
    const style = {
        image: {
            width: "100%",
        },
        wrapper: {
            height:"100%",
            display: "flex",
            alignItems: "center",
        },
    };
    return (
        <div id="wrapper" style={style.wrapper}>
            <img src={swimmer} alt="simmare" style={style.image} />
        </div>
    );
}
